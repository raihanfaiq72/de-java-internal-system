<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EmployeeImport;

class ImportController extends Controller
{
    public function index()
    {
        return view('Import.index');
    }

    public function importEmployee(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            Excel::import(new EmployeeImport, $request->file('file'));
            
            return redirect()
                ->route('import.index')
                ->with('success', 'Data karyawan berhasil diimport!');
                
        } catch (\Exception $e) {
            return redirect()
                ->route('import.index')
                ->with('error', 'Error saat import data karyawan: ' . $e->getMessage());
        }
    }

    public function employeeTemplate()
    {
        $headers = [
            'NIK',
            'Nama Lengkap',
            'Email',
            'No Telepon',
            'Alamat',
            'Departemen',
            'Jabatan',
            'Gaji Harian',
            'Premi',
            'Tanggal Bergabung',
            'Status'
        ];

        $filename = 'template_import_karyawan.csv';
        
        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
