<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\COA;
use App\Models\COAGroup;
use App\Models\COAType;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceItemTax;
use App\Models\Partner;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Tax;
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

class PurchaseImport implements ToCollection, WithHeadingRow, ShouldQueue, WithChunkReading, WithEvents
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
                        'Import Purchase Selesai',
                        'Proses import data purchase telah berhasil diselesaikan.',
                        route('import.index'),
                        'success'
                    ));
                }
            },
            ImportFailed::class => function(ImportFailed $event) {
                $user = User::find($this->userId);
                if ($user) {
                    $user->notify(new SystemNotification(
                        'Import Purchase Gagal',
                        'Terjadi kesalahan saat import data purchase: ' . $event->getException()->getMessage(),
                        route('import.index'),
                        'error'
                    ));
                }
                Log::error('PurchaseImport Failed Event: ' . $event->getException()->getMessage());
            },
        ];
    }

    public function failed(\Throwable $e)
    {
        $user = User::find($this->userId);
        if ($user) {
            $user->notify(new SystemNotification(
                'Import Purchase Gagal',
                'Job import gagal diproses: ' . $e->getMessage(),
                route('import.index'),
                'error'
            ));
        }
        Log::error('PurchaseImport Job Failed: ' . $e->getMessage());
    }

    public function collection(Collection $rows)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        try {
            $officeId = $this->officeId;
            $userId = $this->userId;

            // Ensure default Category exists
            $defaultCategory = ProductCategory::firstOrCreate(
                ['office_id' => $officeId, 'nama_kategori' => 'Uncategorized'],
                ['slug' => 'uncategorized']
            );

            // Ensure default Brand exists
            $defaultBrand = Brand::firstOrCreate(
                ['office_id' => $officeId, 'nama_brand' => 'Generic'],
                ['slug' => 'generic']
            );

            // Ensure default Supplier exists
            $defaultSupplier = Partner::firstOrCreate(
                ['office_id' => $officeId, 'nama' => 'General Supplier'],
                ['tipe_mitra' => 'Supplier', 'status' => 'Active', 'nomor_mitra' => 'SUP-GEN']
            );

            // Fetch a default COA (prefer Inventory/Persediaan)
            $defaultCoa = COA::where('office_id', $officeId)
                ->where('nama_akun', 'like', '%Persediaan%')
                ->first();
                
            if (!$defaultCoa) {
                $defaultCoa = COA::where('office_id', $officeId)->first();
            }

            // If still no COA, create a default one to prevent error
            if (!$defaultCoa) {
                // Ensure Group exists
                $group = \App\Models\COAGroup::firstOrCreate(
                    ['office_id' => $officeId, 'nama_kelompok' => 'Aktiva Lancar'],
                    ['kode_kelompok' => '10000']
                );

                // Ensure Type exists
                $type = \App\Models\COAType::firstOrCreate(
                    ['kelompok_id' => $group->id, 'nama_tipe' => 'Persediaan Barang']
                );

                // Create COA
                $defaultCoa = COA::create([
                    'office_id' => $officeId,
                    'tipe_id' => $type->id,
                    'kode_akun' => '11300',
                    'nama_akun' => 'Persediaan Barang',
                    'is_kas_bank' => 0,
                ]);
            }

            // Group rows by Invoice Number to handle multiple items per invoice
            $invoices = $rows->groupBy(function ($item, $key) {
                return $item['number'] ?? $item['invoice_number'] ?? 'UNKNOWN';
            });

            foreach ($invoices as $invoiceNumber => $items) {
                if ($invoiceNumber === 'UNKNOWN') continue;

                $firstRow = $items->first();
                
                // 1. Get/Create Partner (Supplier)
                $partnerName = $firstRow['partner_name'] ?? $firstRow['mitra'] ?? $firstRow['supplier'] ?? null;
                $partnerId = null;
                
                if ($partnerName) {
                    $cleanName = $partnerName;
                    $badanUsaha = '-';
                    $pattern = '/^(PT|CV|UD|Fa|Firma|Yayasan|Koperasi|Perum|Perjan|Persero)\.?\s+/i';
                    if (preg_match($pattern, $partnerName, $matches)) {
                        $badanUsaha = strtoupper($matches[1]);
                        $cleanName = trim(preg_replace($pattern, '', $partnerName, 1));
                    }

                    $partner = Partner::where('office_id', $officeId)
                        ->where(function($q) use ($cleanName, $partnerName) {
                            $q->where('nama', 'like', "%$cleanName%")
                              ->orWhere('nama', 'like', "%$partnerName%");
                        })->first();

                    if (!$partner) {
                        // Create new Partner (Supplier)
                        $partner = Partner::create([
                            'office_id' => $officeId,
                            'nama' => $cleanName,
                            'badan_usaha' => $badanUsaha,
                            'tipe_mitra' => 'Supplier', // Force Supplier for Purchase Import
                            'status' => 'Active',
                            'nomor_mitra' => $this->generateNomorMitra($officeId),
                        ]);
                    }
                    $partnerId = $partner->id;
                }

                // 2. Parse Dates
                $invoiceDate = $this->parseDate($firstRow['invoice_date'] ?? date('Y-m-d'));
                $dueDate = $this->parseDate($firstRow['due_date'] ?? null);

                // 3. Create Invoice
                $invoice = Invoice::updateOrCreate(
                    [
                        'office_id' => $officeId,
                        'nomor_invoice' => $invoiceNumber,
                    ],
                    [
                        'tipe_invoice' => 'Purchase', // Important: Purchase
                        'tgl_invoice' => $invoiceDate,
                        'tgl_jatuh_tempo' => $dueDate,
                        'mitra_id' => $partnerId,
                        'status_dok' => $this->mapStatusDok($firstRow['status'] ?? 'Draft'),
                        'status_pembayaran' => 'Unpaid', 
                        'sales_id' => $userId, // Created by
                        'ref_no' => $firstRow['document_reference'] ?? null,
                        'currency' => $firstRow['currency'] ?? 'IDR',
                        'subtotal' => 0, 
                        'total_akhir' => 0, 
                        'total_diskon_item' => 0,
                        'is_kop' => 1,
                    ]
                );

                // 4. Process Items
                $subtotal = 0;
                $totalTax = 0;

                // Delete existing items to avoid duplication on re-import
                $invoice->items()->delete();

                foreach ($items as $item) {
                    $productName = $item['product_name'] ?? $item['product'] ?? 'Item Manual';
                    $qty = $this->parseNumber($item['quantity'] ?? 1);
                    $price = $this->parseNumber($item['amount'] ?? 0); 
                    
                    // Logic for Unit Price vs Amount
                    // If 'unit_price' is provided, use it. Else calculate.
                    $unitPrice = $item['unit_price'] ?? ($qty > 0 ? $price / $qty : 0);
                    
                    $discountVal = $this->parseNumber($item['discount'] ?? 0); 
                    $discountAmt = $this->parseNumber($item['discount_amt_per_qty'] ?? 0); 
                    
                    // Logic for Product ID
                    $productId = null;
                    $product = Product::where('office_id', $officeId)
                        ->where('nama_produk', 'like', "%$productName%")
                        ->first();
                    
                    if ($product) {
                        $productId = $product->id;
                        // Update Buying Price? Optional.
                    } else {
                        // Create simple product if not exists
                        $product = Product::create([
                            'office_id' => $officeId,
                            'nama_produk' => $productName,
                            'sku_kode' => 'IMP-' . strtoupper(Str::random(6)),
                            'product_category_id' => $defaultCategory->id,
                            'brand_id' => $defaultBrand->id,
                            'supplier_id' => $partnerId ?? $defaultSupplier->id,
                            'satuan' => 'pcs',
                            'harga_beli' => $unitPrice, // Use as buying price
                            'harga_jual' => $unitPrice * 1.2, // Dummy margin
                            'track_stock' => true,
                            'coa_id' => $defaultCoa ? $defaultCoa->id : null,
                        ]);
                        $productId = $product->id;
                    }

                    // Calculate Line Total
                    $lineTotalBeforeTax = ($qty * $unitPrice) - ($discountAmt * $qty);
                    if ($discountVal > 0) {
                        $lineTotalBeforeTax = $lineTotalBeforeTax * ((100 - $discountVal) / 100);
                    }

                    $invoiceItem = InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'produk_id' => $productId,
                        'nama_produk_manual' => $productName,
                        'deskripsi_produk' => $item['product_description'] ?? null,
                        'qty' => $qty,
                        'harga_satuan' => $unitPrice,
                        'diskon_nilai' => $discountAmt,
                        'total_harga_item' => $lineTotalBeforeTax,
                    ]);

                    // Handle Tax
                    $taxName = $item['tax'] ?? null;
                    if ($taxName) {
                        $tax = Tax::where('office_id', $officeId)->where('nama_pajak', $taxName)->first();
                        if ($tax) {
                            $taxValue = ($lineTotalBeforeTax * $tax->persentase) / 100;
                            InvoiceItemTax::create([
                                'invoice_item_id' => $invoiceItem->id,
                                'tax_id' => $tax->id,
                                'nilai_pajak_diterapkan' => $taxValue,
                            ]);
                            $totalTax += $taxValue;
                        }
                    }

                    $subtotal += $lineTotalBeforeTax;
                }

                // Update Invoice Totals
                $invoice->subtotal = $subtotal;
                $invoice->total_akhir = $subtotal + $totalTax;
                
                $amountDue = $this->parseNumber($firstRow['amount_due'] ?? $invoice->total_akhir);
                if ($amountDue == 0) {
                    $invoice->status_pembayaran = 'Paid';
                } elseif ($amountDue < $invoice->total_akhir) {
                    $invoice->status_pembayaran = 'Partially Paid';
                } else {
                    $invoice->status_pembayaran = 'Unpaid';
                }

                $invoice->save();
            }
        } catch (\Throwable $e) {
            Log::error('PurchaseImport Error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }

    private function mapStatusDok($status)
    {
        $status = strtolower($status);
        if (strpos($status, 'draft') !== false) return 'Draft';
        if (strpos($status, 'sent') !== false) return 'Sent';
        if (strpos($status, 'paid') !== false) return 'Approved';
        if (strpos($status, 'appr') !== false) return 'Approved';
        if (strpos($status, 'fail') !== false) return 'Failed';
        if (strpos($status, 'rej') !== false) return 'Rejected';
        return 'Draft';
    }

    private function parseDate($date)
    {
        if (!$date) return null;
        try {
            return \Carbon\Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseNumber($value)
    {
        if (is_numeric($value)) return $value;
        if (is_string($value)) {
            if (strpos($value, '=') === 0) $value = substr($value, 1);
            $clean = preg_replace('/[^0-9,.-]/', '', $value);
            // Handling IDR format: 1.000,00 vs 1,000.00
            // Simple heuristic: if both exist, assume dot is thousand, comma is decimal (IDR standard) or vice versa.
            // But usually in programmatic export it's standard 1000.00.
            // If comma is last separator, it's decimal (European/IDR).
            if (strpos($clean, ',') !== false && strpos($clean, '.') !== false) {
                 $lastDot = strrpos($clean, '.');
                 $lastComma = strrpos($clean, ',');
                 if ($lastComma > $lastDot) {
                     // 1.000,00
                     $clean = str_replace('.', '', $clean);
                     $clean = str_replace(',', '.', $clean);
                 } else {
                     // 1,000.00
                     $clean = str_replace(',', '', $clean);
                 }
            } elseif (strpos($clean, ',') !== false) {
                 // 1000,00 or 1,000
                 // Assume decimal if 2 digits? risky.
                 // Let's assume standard excel/csv is usually dot decimal.
                 // But user said "IDR", so maybe 1.000.000
                 $clean = str_replace(',', '.', $clean);
            }
            return (float) $clean;
        }
        return 0;
    }

    private function generateNomorMitra($officeId)
    {
        $lastPartner = Partner::where('office_id', $officeId)
            ->where('nomor_mitra', 'like', 'M-%')
            ->orderByRaw('CAST(SUBSTRING(nomor_mitra, 3) AS UNSIGNED) DESC')
            ->first();

        $nextId = 1;
        if ($lastPartner) {
            $lastNumber = (int) substr($lastPartner->nomor_mitra, 2);
            $nextId = $lastNumber + 1;
        }

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
