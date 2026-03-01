<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Mitra;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StockLocation;
use App\Models\StockMutation;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportController extends Controller
{
    public function index()
    {
        return view('Import.index');
    }

    public function importStock(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();
        
        // Check if Maatwebsite Excel is installed
        if (class_exists('Maatwebsite\Excel\Facades\Excel')) {
            try {
                // Use Maatwebsite Excel
                \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\StockImport, $file);
                return redirect()->back()->with('success', 'Data stok berhasil diimport!');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
            }
        }
        
        return redirect()->back()->with('error', 'Library Excel tidak terinstall.');
    }

    public function downloadTemplate($type)
    {
        if ($type === 'stock') {
            return $this->downloadStockTemplate();
        }

        return redirect()->back()->with('error', 'Template tidak ditemukan.');
    }

    private function downloadStockTemplate()
    {
        $fileName = 'Template_Import_Stok.xlsx';
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        // Create a simple Excel file using Maatwebsite
        if (class_exists('Maatwebsite\Excel\Facades\Excel')) {
            return \Maatwebsite\Excel\Facades\Excel::download(new class implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\ShouldAutoSize {
                public function array(): array
                {
                    return [
                        [
                            'SKU-001', 'Contoh Produk A', 'Makanan', 'Indofood', 'PT. Supplier Jaya', 'pcs', 10000, 12000, 50, 'Gudang Utama'
                        ],
                        [
                            'SKU-002', 'Contoh Produk B', 'Minuman', 'Ultrajaya', 'CV. Mitra Abadi', 'dus', 50000, 60000, 10, 'Gudang Utama'
                        ]
                    ];
                }

                public function headings(): array
                {
                    return [
                        'sku', 'nama_produk', 'kategori', 'brand', 'supplier', 'satuan', 'harga_beli', 'harga_jual', 'stok', 'lokasi'
                    ];
                }

                public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
                {
                    return [
                        1 => ['font' => ['bold' => true]],
                    ];
                }
            }, $fileName);
        }

        return redirect()->back()->with('error', 'Library Excel tidak terinstall.');
    }
}
