<?php

namespace App\Imports;

use App\Models\Mitra;
use App\Models\Partner;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use App\Models\User;
use App\Notifications\SystemNotification;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\ImportFailed;

class MitraImport implements ToCollection, WithHeadingRow, ShouldQueue, WithChunkReading, WithEvents
{
    protected $officeId;
    protected $userId;

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
            AfterImport::class => function(AfterImport $event) {
                $user = User::find($this->userId);
                if ($user) {
                    $user->notify(new SystemNotification(
                        'Import Mitra Selesai',
                        'Proses import data mitra telah berhasil diselesaikan.',
                        route('import.index'),
                        'success'
                    ));
                }
            },
            ImportFailed::class => function(ImportFailed $event) {
                $user = User::find($this->userId);
                if ($user) {
                    $user->notify(new SystemNotification(
                        'Import Mitra Gagal',
                        'Terjadi kesalahan saat import data mitra: ' . $event->getException()->getMessage(),
                        route('import.index'),
                        'error'
                    ));
                }
                Log::error('MitraImport Failed Event: ' . $event->getException()->getMessage());
            },
        ];
    }

    public function failed(\Throwable $e)
    {
        $user = User::find($this->userId);
        if ($user) {
            $user->notify(new SystemNotification(
                'Import Mitra Gagal',
                'Job import gagal diproses: ' . $e->getMessage(),
                route('import.index'),
                'error'
            ));
        }
        Log::error('MitraImport Job Failed: ' . $e->getMessage());
    }

    public function collection(Collection $rows)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        try {
            $officeId = $this->officeId;

            foreach ($rows as $row) {
                $rawName = $row['nama'] ?? $row['nama_mitra'] ?? $row['partner_name'] ?? null;
                
                if (!$rawName) continue;

                // Detect Badan Usaha
                $badanUsaha = '-';
                $cleanName = $rawName;

                // Regex to detect common Indonesian business entities at the start of the string
                // Case insensitive, with or without dot, followed by space
                $pattern = '/^(PT|CV|UD|Fa|Firma|Yayasan|Koperasi|Perum|Perjan|Persero)\.?\s+/i';
                
                if (preg_match($pattern, $rawName, $matches)) {
                    // $matches[1] will contain the entity (e.g., "PT", "cv")
                    // Normalize to uppercase or keep as matched? usually PT/CV is uppercase.
                    $detectedEntity = strtoupper($matches[1]);
                    
                    // Special casing if needed (e.g. Fa -> Firma?) Let's keep it simple.
                    $badanUsaha = $detectedEntity;
                    
                    // Remove the entity from the name
                    // preg_replace to remove the match found at start
                    $cleanName = preg_replace($pattern, '', $rawName, 1);
                }

                $cleanName = trim($cleanName);

                // Generate Nomor Mitra if needed
                $nomorMitra = $row['no_mitra'] ?? $row['nomor_mitra'] ?? $row['kode'] ?? $this->generateNomorMitra($officeId);

                // Map Tipe Mitra
                $tipe = $row['type'] ?? $row['tipe'] ?? $row['tipe_mitra'] ?? 'Client'; // Default Client
                
                // Normalize tipe to match ENUM ('Supplier', 'Client', 'Both')
                if (stripos($tipe, 'supp') !== false) {
                    $tipe = 'Supplier';
                } elseif (stripos($tipe, 'cust') !== false || stripos($tipe, 'client') !== false) {
                    $tipe = 'Client';
                } elseif (stripos($tipe, 'both') !== false) {
                    $tipe = 'Both';
                } else {
                    // Fallback to Client if unknown (assuming "Customer" maps to "Client")
                    $tipe = 'Client';
                }

                Partner::updateOrCreate(
                    [
                        'office_id' => $officeId, 
                        'nama' => $cleanName,
                    ],
                    [
                        'nomor_mitra' => $nomorMitra, 
                        'badan_usaha' => $badanUsaha,
                        'tipe_mitra' => $tipe,
                        'no_hp' => $row['telp'] ?? $row['no_hp'] ?? $row['hp'] ?? $row['phone'] ?? null,
                        'email' => $row['email'] ?? null,
                        'alamat' => $row['alamat'] ?? $row['address'] ?? null,
                        'ktp_npwp' => $row['ktp'] ?? $row['npwp'] ?? $row['ktp_npwp'] ?? null,
                        'kontak_nama' => $row['pic'] ?? $row['kontak'] ?? $row['contact_person'] ?? null,
                        'status' => 'Active',
                        'is_cash_customer' => false,
                    ]
                );
            }
        } catch (\Throwable $e) {
            Log::error('MitraImport Error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }

    private function generateNomorMitra($officeId)
    {
        // Find the highest number used in M-XXXXX format
        // We use a raw query to extract the number part for correct sorting
        $lastPartner = Partner::where('office_id', $officeId)
            ->where('nomor_mitra', 'like', 'M-%')
            ->orderByRaw('CAST(SUBSTRING(nomor_mitra, 3) AS UNSIGNED) DESC')
            ->first();

        $nextId = 1;
        if ($lastPartner) {
            // Extract number from M-XXXXX
            $lastNumber = (int) substr($lastPartner->nomor_mitra, 2);
            $nextId = $lastNumber + 1;
        }

        // Ensure uniqueness (loop to find next available)
        do {
            $code = 'M-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
            $exists = Partner::where('office_id', $officeId)->where('nomor_mitra', $code)->exists();
            if ($exists) {
                $nextId++;
            }
        } while ($exists);

        return $code;
    }
}
