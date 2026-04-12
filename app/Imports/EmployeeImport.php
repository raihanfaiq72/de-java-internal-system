<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\User;
use App\Notifications\SystemNotification;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\ImportFailed;

class EmployeeImport implements ShouldQueue, ToCollection, WithChunkReading, WithEvents, WithHeadingRow
{
    protected $officeId;

    protected $userId;

    protected $processedCount = 0;

    public static $mandatoryHeaders = ['name', 'position', 'daily_salary'];

    public function __construct($officeId, $userId)
    {
        $this->officeId = $officeId;
        $this->userId = $userId;
    }

    public function chunkSize(): int
    {
        return 200;
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function (AfterImport $event) {
                $user = User::find($this->userId);
                if ($user) {
                    $user->notify(new SystemNotification(
                        'Import Karyawan Selesai',
                        "Proses import data karyawan telah berhasil diselesaikan. {$this->processedCount} karyawan dimasukkan.",
                        route('import.index'),
                        'success'
                    ));
                }
            },
            ImportFailed::class => function (ImportFailed $event) {
                $user = User::find($this->userId);
                if ($user) {
                    $user->notify(new SystemNotification(
                        'Import Karyawan Gagal',
                        'Terjadi kesalahan saat import data karyawan: '.$event->getException()->getMessage(),
                        route('import.index'),
                        'error'
                    ));
                }
                Log::error('EmployeeImport Failed Event: '.$event->getException()->getMessage());
            },
        ];
    }

    public function failed(\Throwable $e)
    {
        $user = User::find($this->userId);
        if ($user) {
            $user->notify(new SystemNotification(
                'Import Karyawan Gagal',
                'Job import gagal diproses: '.$e->getMessage(),
                route('import.index'),
                'error'
            ));
        }
        Log::error('EmployeeImport Job Failed: '.$e->getMessage());
    }

    public function collection(Collection $rows)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        try {
            $officeId = $this->officeId;

            // 1. Validate Header (Strict Check)
            if ($rows->isEmpty()) {
                return;
            }
            $firstRow = $rows->first();
            $requiredColumns = ['name', 'position', 'daily_salary'];
            foreach ($requiredColumns as $col) {
                if (! isset($firstRow[$col]) && ! array_key_exists($col, $firstRow->toArray())) {
                    throw new \Exception("Template tidak sesuai. Kolom wajib '$col' tidak ditemukan di tab Karyawan.");
                }
            }

            foreach ($rows as $row) {
                $name = $row['name'] ?? $row['nama'] ?? $row['nama_karyawan'] ?? null;

                if (! $name) {
                    continue;
                }

                // Generate NIK if not provided
                $nik = $row['nik'] ?? $row['no_induk'] ?? $this->generateNIK($officeId);

                // Parse and validate dates
                $joinDate = $this->parseDate($row['join_date'] ?? $row['tanggal_gabung'] ?? $row['tgl_gabung'] ?? null);

                // Parse salary values
                $dailySalary = $this->parseNumber($row['daily_salary'] ?? $row['gaji_harian'] ?? $row['gaji'] ?? 0);
                $premi = $this->parseNumber($row['premi'] ?? $row['tunjangan'] ?? 0);

                // Normalize status
                $status = $row['status'] ?? $row['status_karyawan'] ?? 'Active';
                if (stripos($status, 'aktif') !== false || stripos($status, 'active') !== false) {
                    $status = 'Active';
                } elseif (stripos($status, 'tidak') !== false || stripos($status, 'non') !== false || stripos($status, 'inactive') !== false) {
                    $status = 'Inactive';
                } else {
                    $status = 'Active'; // Default to Active
                }

                // Find or create user
                $userId = null;
                $username = $row['username'] ?? $row['user_name'] ?? null;
                $email = $row['email'] ?? $row['user_email'] ?? null;

                if (! $username && $name) {
                    $username = str_replace(' ', '.', strtolower(trim($name)));
                }

                if ($username || $email) {
                    $user = User::where(function ($q) use ($username, $email) {
                        if ($username) {
                            $q->where('username', $username);
                        }
                        if ($email) {
                            $q->orWhere('email', $email);
                        }
                    })->first();

                    if (! $user) {
                        // Create a basic user account
                        $user = User::create([
                            'name' => $name,
                            'username' => $username,
                            'email' => $email ?? ($username.'@example.com'),
                            'password' => bcrypt('password123'), // Default password
                            'email_verified_at' => now(),
                        ]);
                    }
                    $userId = $user->id;
                }

                Employee::updateOrCreate(
                    [
                        'nik' => $nik,
                    ],
                    [
                        'user_id' => $userId,
                        'name' => $name,
                        'position' => $row['position'] ?? $row['jabatan'] ?? $row['posisi'] ?? 'Karyawan',
                        'daily_salary' => $dailySalary,
                        'premi' => $premi,
                        'join_date' => $joinDate,
                        'status' => $status,
                    ]
                );
                $this->processedCount++;
            }
        } catch (\Throwable $e) {
            Log::error('EmployeeImport Error: '.$e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }

    private function generateNIK($officeId)
    {
        // Find the highest NIK number in EMP-XXXXX format
        $lastEmployee = Employee::where('nik', 'like', 'EMP-%')
            ->orderByRaw('CAST(SUBSTRING(nik, 5) AS UNSIGNED) DESC')
            ->first();

        $nextId = 1;
        if ($lastEmployee) {
            // Extract number from EMP-XXXXX
            $lastNumber = (int) substr($lastEmployee->nik, 4);
            $nextId = $lastNumber + 1;
        }

        // Ensure uniqueness
        do {
            $code = 'EMP-'.str_pad($nextId, 5, '0', STR_PAD_LEFT);
            $exists = Employee::where('nik', $code)->exists();
            if ($exists) {
                $nextId++;
            }
        } while ($exists);

        return $code;
    }

    private function parseDate($value)
    {
        if (! $value) {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value;
        }

        if (is_string($value)) {
            try {
                // Handle various date formats
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                    return Carbon::createFromFormat('Y-m-d', $value);
                } elseif (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
                    return Carbon::createFromFormat('d/m/Y', $value);
                } elseif (preg_match('/^\d{2}-\d{2}-\d{4}$/', $value)) {
                    return Carbon::createFromFormat('d-m-Y', $value);
                } else {
                    // Try to parse with Carbon's flexible parsing
                    return Carbon::parse($value);
                }
            } catch (\Exception $e) {
                Log::warning('Could not parse date: '.$value);

                return null;
            }
        }

        return null;
    }

    private function parseNumber($value)
    {
        if (is_numeric($value)) {
            return (float) $value;
        }
        if (! $value) {
            return 0;
        }

        $clean = str_replace(['Rp', ' ', "\xc2\xa0"], '', $value);
        $clean = preg_replace('/[^0-9,.-]/', '', $clean);

        $lastDot = strrpos($clean, '.');
        $lastComma = strrpos($clean, ',');

        if ($lastDot !== false && $lastComma !== false) {
            if ($lastComma > $lastDot) {
                // Format 1.000,00 (IDR)
                $clean = str_replace('.', '', $clean);
                $clean = str_replace(',', '.', $clean);
            } else {
                // Format 1,000.00 (EN)
                $clean = str_replace(',', '', $clean);
            }
        } elseif ($lastDot !== false) {
            // Hanya ada titik.
            if (substr_count($clean, '.') > 1 || strlen(substr($clean, $lastDot + 1)) === 3) {
                $clean = str_replace('.', '', $clean);
            }
        } elseif ($lastComma !== false) {
            // Hanya ada koma.
            if (substr_count($clean, ',') > 1 || strlen(substr($clean, $lastComma + 1)) === 3) {
                $clean = str_replace(',', '', $clean);
            } else {
                $clean = str_replace(',', '.', $clean);
            }
        }

        return (float) $clean;
    }
}
