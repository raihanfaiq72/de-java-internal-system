<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function importStock(Request $request)
    {
        return $this->handleImport($request, \App\Imports\StockImport::class, [session('active_office_id'), auth()->id()]);
    }

    public function importMitra(Request $request)
    {
        return $this->handleImport($request, \App\Imports\MitraImport::class, [session('active_office_id'), auth()->id()]);
    }

    public function importSales(Request $request)
    {
        return $this->handleImport($request, \App\Imports\SalesImport::class, [session('active_office_id'), auth()->id()]);
    }

    public function importPurchase(Request $request)
    {
        return $this->handleImport($request, \App\Imports\PurchaseImport::class, [session('active_office_id'), auth()->id()]);
    }

    public function importReceipt(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        $request->validate([
            'file.*' => 'required|file|mimes:pdf',
        ]);

        $files = $request->file('file');
        $officeId = session('active_office_id');
        $userId = auth()->id();

        if (! $files) {
            return redirect()->back()->with('error', 'Tidak ada file yang dipilih.');
        }

        if (! is_array($files)) {
            $files = [$files];
        }

        $count = 0;
        foreach ($files as $file) {
            $path = $file->store('temp_receipts');
            $originalName = $file->getClientOriginalName();
            \App\Jobs\ImportReceiptJob::dispatch($path, $originalName, $officeId, $userId);
            $count++;
        }

        return redirect()->back()->with('success', $count.' File PDF sedang diproses di background.');
    }

    public function importPurchaseReceipt(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        $request->validate([
            'file.*' => 'required|file|mimes:pdf',
        ]);

        $files = $request->file('file');
        $officeId = session('active_office_id');
        $userId = auth()->id();

        if (! $files) {
            return redirect()->back()->with('error', 'Tidak ada file yang dipilih.');
        }

        if (! is_array($files)) {
            $files = [$files];
        }

        $count = 0;
        foreach ($files as $file) {
            $path = $file->store('temp_receipts');
            $originalName = $file->getClientOriginalName();
            \App\Jobs\ImportPurchaseReceiptJob::dispatch($path, $originalName, $officeId, $userId);
            $count++;
        }

        return redirect()->back()->with('success', $count.' File PDF Purchase Receipt sedang diproses di background.');
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
        if ($type === 'purchase') {
            return $this->downloadPurchaseTemplate();
        }

        return redirect()->back()->with('error', 'Template tidak ditemukan.');
    }

    private function handleImport(Request $request, $importClass, $params = [])
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('file');

        if (class_exists('Maatwebsite\Excel\Facades\Excel')) {
            try {
                $importInstance = new $importClass(...$params);
                \Maatwebsite\Excel\Facades\Excel::queueImport($importInstance, $file);

                return redirect()->back()->with('success', 'Data sedang diproses di background. Silakan cek beberapa saat lagi.');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal memulai import: '.$e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'Library Excel tidak terinstall.');
    }

    private function downloadSalesTemplate()
    {
        $fileName = 'Template_Import_Sales.xlsx';

        if (class_exists('Maatwebsite\Excel\Facades\Excel')) {
            return \Maatwebsite\Excel\Facades\Excel::download(new class implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\ShouldAutoSize, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithStyles
            {
                public function array(): array
                {
                    return [
                        [
                            date('Y-m-d'), date('Y-m-d', strtotime('+30 days')), 'INV-2023-001', 'Sent', 'PT. Pelanggan Setia', 'IDR',
                            1100000, 1100000, 'Jasa Konsultasi', 'Konsultasi IT Bulan Januari', 1, 0, 0, 'PPN 11%', 1000000, 'PO-001',
                        ],
                        [
                            date('Y-m-d'), date('Y-m-d', strtotime('+30 days')), 'INV-2023-002', 'Draft', 'CV. Mitra Baru', 'IDR',
                            550000, 550000, 'Produk A', 'Deskripsi Produk A', 5, 0, 0, 'PPN 11%', 500000, 'PO-002',
                        ],
                    ];
                }

                public function headings(): array
                {
                    return [
                        'invoice_date', 'due_date', 'number', 'status', 'partner_name', 'currency',
                        'amount_due', 'grand_total', 'product_name', 'product_description', 'quantity', 'discount', 'discount_amt_per_qty', 'tax', 'amount', 'document_reference',
                    ];
                }

                public function styles($sheet)
                {
                    return [
                        1 => ['font' => ['bold' => true]],
                    ];
                }
            }, $fileName);
        }

        return redirect()->back()->with('error', 'Library Excel tidak terinstall.');
    }

    private function downloadPurchaseTemplate()
    {
        $fileName = 'Template_Import_Purchase.xlsx';

        if (class_exists('Maatwebsite\Excel\Facades\Excel')) {
            return \Maatwebsite\Excel\Facades\Excel::download(new class implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\ShouldAutoSize, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithStyles
            {
                public function array(): array
                {
                    return [
                        [
                            date('Y-m-d'), date('Y-m-d', strtotime('+30 days')), 'PINV-2023-001', 'Sent', 'PT. Supplier Jaya', 'IDR',
                            1100000, 1100000, 'Bahan Baku X', 'Material X Grade A', 10, 0, 0, 'PPN 11%', 1000000, 'PO-REF-001',
                        ],
                        [
                            date('Y-m-d'), date('Y-m-d', strtotime('+30 days')), 'PINV-2023-002', 'Draft', 'CV. Vendor Maju', 'IDR',
                            550000, 550000, 'Jasa Service', 'Service AC', 1, 0, 0, 'PPN 11%', 500000, 'PO-REF-002',
                        ],
                    ];
                }

                public function headings(): array
                {
                    return [
                        'invoice_date', 'due_date', 'number', 'status', 'supplier', 'currency',
                        'amount_due', 'grand_total', 'product_name', 'product_description', 'quantity', 'discount', 'discount_amt_per_qty', 'tax', 'amount', 'document_reference',
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

        if (class_exists('Maatwebsite\Excel\Facades\Excel')) {
            return \Maatwebsite\Excel\Facades\Excel::download(new class implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\ShouldAutoSize, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithStyles
            {
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

        if (class_exists('Maatwebsite\Excel\Facades\Excel')) {
            return \Maatwebsite\Excel\Facades\Excel::download(new class implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\ShouldAutoSize, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithStyles
            {
                public function array(): array
                {
                    return [
                        ['SKU-001', 'Contoh Produk A', 'Makanan', 'Indofood', 'PT. Supplier Jaya', 'pcs', 10000, 12000, 50, 'Gudang Utama'],
                        ['SKU-002', 'Contoh Produk B', 'Minuman', 'Ultrajaya', 'CV. Mitra Abadi', 'dus', 50000, 60000, 10, 'Gudang Utama'],
                    ];
                }

                public function headings(): array
                {
                    return ['sku', 'nama_produk', 'kategori', 'brand', 'supplier', 'satuan', 'harga_beli', 'harga_jual', 'stok', 'lokasi'];
                }

                public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
                {
                    return [1 => ['font' => ['bold' => true]]];
                }
            }, $fileName);
        }

        return redirect()->back()->with('error', 'Library Excel tidak terinstall.');
    }
}
