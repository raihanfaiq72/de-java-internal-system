<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('employee');

        if ($request->date) {
            $query->whereDate('date', $request->date);
        } else {
            $query->whereDate('date', now());
        }

        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        $attendances = $query->latest('date')->paginate(10);
        $employees = Employee::orderBy('name')->get();

        return view('Attendances.index', compact('attendances', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'status' => 'required|in:present,sick,permission,alpha,late',
            'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i|after:clock_in',
            'note' => 'nullable|string',
        ]);

        Attendance::create($request->all());

        return redirect()->back()->with('success', 'Absensi berhasil ditambahkan');
    }

    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'status' => 'required|in:present,sick,permission,alpha,late',
            'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i|after:clock_in',
            'note' => 'nullable|string',
        ]);

        $attendance->update($request->all());

        return redirect()->back()->with('success', 'Absensi berhasil diperbarui');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->back()->with('success', 'Absensi berhasil dihapus');
    }

    public function template()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_absensi.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['employee_nik', 'date', 'clock_in', 'clock_out', 'status', 'note']);
            fputcsv($handle, ['EMP001', '2026-02-16', '08:00', '17:00', 'present', 'Contoh dari mesin fingerprint']);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');

        if (!$handle) {
            return redirect()->back()->with('error', 'File tidak dapat dibaca');
        }

        $header = fgetcsv($handle, 0, ',');
        if (!$header) {
            fclose($handle);
            return redirect()->back()->with('error', 'Header file tidak ditemukan');
        }

        $header = array_map('strtolower', array_map('trim', $header));

        $indexNik = array_search('employee_nik', $header);
        $indexDate = array_search('date', $header);
        $indexClockIn = array_search('clock_in', $header);
        $indexClockOut = array_search('clock_out', $header);
        $indexStatus = array_search('status', $header);
        $indexNote = array_search('note', $header);

        if ($indexNik === false || $indexDate === false) {
            fclose($handle);
            return redirect()->back()->with('error', 'Kolom employee_nik dan date wajib ada di header');
        }

        $allowedStatus = ['present', 'late', 'sick', 'permission', 'alpha'];

        $imported = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            if (count($row) === 0 || trim(implode('', $row)) === '') {
                continue;
            }

            $nik = isset($row[$indexNik]) ? trim($row[$indexNik]) : null;
            $dateRaw = isset($row[$indexDate]) ? trim($row[$indexDate]) : null;

            if (!$nik || !$dateRaw) {
                $skipped++;
                continue;
            }

            $employee = Employee::where('nik', $nik)->first();
            if (!$employee) {
                $skipped++;
                continue;
            }

            try {
                $date = Carbon::parse($dateRaw)->toDateString();
            } catch (\Throwable $e) {
                $skipped++;
                continue;
            }

            $clockIn = $indexClockIn !== false && isset($row[$indexClockIn]) ? trim($row[$indexClockIn]) : null;
            $clockOut = $indexClockOut !== false && isset($row[$indexClockOut]) ? trim($row[$indexClockOut]) : null;
            $statusRaw = $indexStatus !== false && isset($row[$indexStatus]) ? trim(strtolower($row[$indexStatus])) : null;
            $note = $indexNote !== false && isset($row[$indexNote]) ? trim($row[$indexNote]) : null;

            if ($statusRaw && in_array($statusRaw, $allowedStatus)) {
                $status = $statusRaw;
            } else {
                $status = 'present';
            }

            Attendance::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'date' => $date,
                ],
                [
                    'clock_in' => $clockIn ?: null,
                    'clock_out' => $clockOut ?: null,
                    'status' => $status,
                    'note' => $note,
                ]
            );

            $imported++;
        }

        fclose($handle);

        return redirect()->back()->with('success', "Import selesai. Berhasil: {$imported}, Dilewati: {$skipped}");
    }
}
