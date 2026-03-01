<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\COA;
use App\Models\COAGroup;
use App\Models\COAType;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceItemTax;
use App\Models\Mitra;
use App\Models\Partner;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Tax;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SalesImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $officeId = session('active_office_id');
        $userId = auth()->id();

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
            
            // 1. Get/Create Partner
            $partnerName = $firstRow['partner_name'] ?? $firstRow['mitra'] ?? null;
            $partnerId = null;
            
            if ($partnerName) {
                // Try to find partner
                // Use logic similar to MitraImport for cleaning name if needed, or exact match
                // For now, let's try fuzzy search or create
                // Extract entity if possible to match better
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
                    // Create new Partner
                    $partner = Partner::create([
                        'office_id' => $officeId,
                        'nama' => $cleanName,
                        'badan_usaha' => $badanUsaha,
                        'tipe_mitra' => 'Client',
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
                    'tipe_invoice' => 'Sales',
                    'tgl_invoice' => $invoiceDate,
                    'tgl_jatuh_tempo' => $dueDate,
                    'mitra_id' => $partnerId,
                    'status_dok' => $this->mapStatusDok($firstRow['status'] ?? 'Draft'),
                    'status_pembayaran' => 'Unpaid', // Default, logic to determine paid is complex without payment data
                    'sales_id' => $userId, // Current user
                    'ref_no' => $firstRow['document_reference'] ?? null,
                    'currency' => $firstRow['currency'] ?? 'IDR',
                    'subtotal' => 0, // Will calculate
                    'total_akhir' => 0, // Will calculate
                    'total_diskon_item' => 0,
                    'is_kop' => 1,
                    'perlu_acc_admin' => 0,
                ]
            );

            // 4. Process Items
            $subtotal = 0;
            $totalDiscount = 0;
            $totalTax = 0;
            $totalGrand = 0;

            // Delete existing items to avoid duplication on re-import
            $invoice->items()->delete();

            foreach ($items as $item) {
                $productName = $item['product_name'] ?? $item['product'] ?? 'Item Manual';
                $qty = $this->parseNumber($item['quantity'] ?? 1);
                $price = $this->parseNumber($item['amount'] ?? 0); // Assuming Amount is Price/Unit? Or Total?
                // Usually "Amount" in export is Line Total. "Rate" or "Price" is unit price.
                // Template says: amount_due, grand_total (header), but item level: amount?
                // Let's assume 'amount' is line total, and we need unit price.
                // If 'unit_price' exists use it, otherwise calc from amount / qty
                $unitPrice = $item['unit_price'] ?? ($qty > 0 ? $price / $qty : 0);
                
                // If template provides discount per qty or percent
                $discountVal = $this->parseNumber($item['discount'] ?? 0); // Percent?
                $discountAmt = $this->parseNumber($item['discount_amt_per_qty'] ?? 0); 
                
                // Logic for Product ID
                $productId = null;
                $product = Product::where('office_id', $officeId)
                    ->where('nama_produk', 'like', "%$productName%")
                    ->first();
                
                if ($product) {
                    $productId = $product->id;
                } else {
                    // Create simple product if not exists? Or just keep as manual text?
                    // User said: "pastikan saat import juga menyocokan ke situ agar mendapatkan id yang benar , kalau belum ada ya buat master dulu"
                    // So we create it.
                    $product = Product::create([
                        'office_id' => $officeId,
                        'nama_produk' => $productName,
                        'sku_kode' => 'IMP-' . strtoupper(Str::random(6)),
                        'product_category_id' => $defaultCategory->id,
                        'brand_id' => $defaultBrand->id,
                        'supplier_id' => $defaultSupplier->id,
                        'satuan' => 'pcs',
                        'harga_jual' => $unitPrice,
                        'track_stock' => true,
                        'coa_id' => $defaultCoa ? $defaultCoa->id : null,
                    ]);
                    $productId = $product->id;
                }

                // Calculate Line Total
                // Total = (Qty * Price) - Discount
                $lineTotalBeforeTax = ($qty * $unitPrice) - ($discountAmt * $qty);
                // If discount is percent
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
                    'diskon_nilai' => $discountAmt, // Storing fixed discount per item
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
                        // Add tax to line total? DB schema usually separates it or includes it.
                        // InvoiceItem total_harga_item usually excludes tax in many systems, but let's check InvoiceItem model.
                        // It doesn't specify. Assuming Excl Tax.
                    }
                }

                $subtotal += $lineTotalBeforeTax;
            }

            // Update Invoice Totals
            $invoice->subtotal = $subtotal;
            $invoice->total_akhir = $subtotal + $totalTax; // + Shipping etc if exist
            // Override with imported Grand Total if matches (to handle rounding diffs)
            $importedGrandTotal = $this->parseNumber($firstRow['grand_total'] ?? 0);
            if ($importedGrandTotal > 0) {
                // Maybe store difference as rounding adjustment?
                // For now, let's trust calculation based on items to be consistent.
            }
            
            // Map amount_due to status_pembayaran
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
    }

    private function mapStatusDok($status)
    {
        // Map excel status to Enum
        $status = strtolower($status);
        if (strpos($status, 'draft') !== false) return 'Draft';
        if (strpos($status, 'sent') !== false) return 'Sent';
        if (strpos($status, 'paid') !== false) return 'Approved'; // Paid implies Approved/Posted
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
            if (strpos($clean, ',') !== false && strpos($clean, '.') !== false) {
                 // 1.000,00 -> 1000.00
                 $clean = str_replace('.', '', $clean);
                 $clean = str_replace(',', '.', $clean);
            } elseif (strpos($clean, ',') !== false) {
                 // 1000,00 -> 1000.00
                 $clean = str_replace(',', '.', $clean);
            }
            return (float) $clean;
        }
        return 0;
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
