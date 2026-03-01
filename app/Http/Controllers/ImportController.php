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
        return $this->handleImport($request, \App\Imports\StockImport::class, [session('active_office_id')]);
    }

    public function importMitra(Request $request)
    {
        return $this->handleImport($request, \App\Imports\MitraImport::class, [session('active_office_id')]);
    }

    public function importSales(Request $request)
    {
        return $this->handleImport($request, \App\Imports\SalesImport::class, [session('active_office_id'), auth()->id()]);
    }

    private function handleImport(Request $request, $importClass, $params = [])
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('file');
        
        if (class_exists('Maatwebsite\Excel\Facades\Excel')) {
            try {
                // Instantiate the import class with parameters
                // For SalesImport, we need both officeId and userId
                // For others, just officeId
                
                // We need to store the file temporarily to queue it properly
                // Or let Excel::queueImport handle it.
                // However, queueImport needs an object instance.
                
                $importInstance = new $importClass(...$params);
                
                \Maatwebsite\Excel\Facades\Excel::queueImport($importInstance, $file);
                return redirect()->back()->with('success', 'Data sedang diproses di background. Silakan cek beberapa saat lagi.');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal memulai import: ' . $e->getMessage());
            }
        }
        
        return redirect()->back()->with('error', 'Library Excel tidak terinstall.');
    }

    public function downloadTemplate($type)
    {
        if ($type === 'stock') {
            return $this->downloadStockTemplate();
        }
        if ($type === 'mitra') {
            return $this->downloadMitraTemplate();
        }
        if ($type === 'sales') {
            return $this->downloadSalesTemplate();
        }

        return redirect()->back()->with('error', 'Template tidak ditemukan.');
    }

    private function downloadSalesTemplate()
    {
        $fileName = 'Template_Import_Sales.xlsx';
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        if (class_exists('Maatwebsite\Excel\Facades\Excel')) {
            return \Maatwebsite\Excel\Facades\Excel::download(new class implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\ShouldAutoSize {
                public function array(): array
                {
                    return [
                        [
                            date('Y-m-d'), date('Y-m-d', strtotime('+30 days')), 'INV-2023-001', 'Sent', 'PT. Pelanggan Setia', 'IDR', 
                            1100000, 1100000, 'Jasa Konsultasi', 'Konsultasi IT Bulan Januari', 1, 0, 0, 'PPN 11%', 1000000, 'PO-001'
                        ],
                        [
                            date('Y-m-d'), date('Y-m-d', strtotime('+30 days')), 'INV-2023-002', 'Draft', 'CV. Mitra Baru', 'IDR', 
                            550000, 550000, 'Produk A', 'Deskripsi Produk A', 5, 0, 0, 'PPN 11%', 500000, 'PO-002'
                        ],
                    ];
                }

                public function headings(): array
                {
                    return [
                        'invoice_date', 'due_date', 'number', 'status', 'partner_name', 'currency', 'amount_due', 'grand_total', 
                        'product_name', 'product_description', 'quantity', 'discount', 'discount_amt_per_qty', 'tax', 'amount', 'document_reference'
                    ];
                }

                public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
                {
                    return [1 => ['font' => ['bold' => true]]];
                }
            }, $fileName);
        }
        return redirect()->back()->with('error', 'Library Excel tidak terinstall.');
    }

    private function downloadMitraTemplate()
    {
        $fileName = 'Template_Import_Mitra.xlsx';
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        if (class_exists('Maatwebsite\Excel\Facades\Excel')) {
            return \Maatwebsite\Excel\Facades\Excel::download(new class implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\ShouldAutoSize {
                public function array(): array
                {
                    return [
                        ['Client', 'M-0001', 'PT. Maju Jaya', '08123456789'],
                        ['Supplier', 'S-0001', 'CV. Abadi Sentosa', '08987654321'],
                        ['Client', '', 'Toko Berkah', '08111222333'],
                    ];
                }

                public function headings(): array
                {
                    // Minimal required fields: Type, No Mitra, Nama, Telp
                    return ['Type', 'No Mitra', 'Nama', 'Telp'];
                }

                public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
                {
                    return [1 => ['font' => ['bold' => true]]];
                }
            }, $fileName);
        }
        return redirect()->back()->with('error', 'Library Excel tidak terinstall.');
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
