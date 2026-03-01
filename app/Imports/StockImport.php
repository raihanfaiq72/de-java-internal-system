<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\Partner;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StockLocation;
use App\Models\StockMutation;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

use App\Models\COA;

class StockImport implements ToCollection, WithHeadingRow, ShouldQueue, WithChunkReading
{
    protected $officeId;

    public function __construct($officeId)
    {
        $this->officeId = $officeId;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function collection(Collection $rows)
    {
        $officeId = $this->officeId;
        $defaultLocation = StockLocation::firstOrCreate(
            ['office_id' => $officeId, 'is_active' => true],
            ['name' => 'Gudang Utama', 'type' => 'stock']
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

        foreach ($rows as $row) {
            // Flexible Column Mapping
            $sku = $row['sku'] ?? $row['kode'] ?? $row['kode_barang'] ?? $row['sku_kode'] ?? null;
            $name = $row['nama'] ?? $row['nama_produk'] ?? $row['produk'] ?? $row['nama_barang'] ?? null;
            
            if (!$name) continue;

            // Generate SKU if missing or "Belum di set"
            if (!$sku || strtolower($sku) === 'belum di set') {
                // Try to generate from Name slug + random string
                $baseSlug = Str::slug($name);
                $sku = strtoupper(substr($baseSlug, 0, 3) . '-' . mt_rand(1000, 9999));
                
                // Ensure unique
                while (Product::where('sku_kode', $sku)->where('office_id', $officeId)->exists()) {
                    $sku = strtoupper(substr($baseSlug, 0, 3) . '-' . mt_rand(1000, 9999));
                }
            }

            // 1. Category
            $categoryName = $row['kategori'] ?? $row['category'] ?? 'Uncategorized';
            if (strtolower($categoryName) === 'belum di set') $categoryName = 'Uncategorized';
            
            $category = ProductCategory::firstOrCreate(
                ['nama_kategori' => $categoryName, 'office_id' => $officeId],
                ['slug' => Str::slug($categoryName)]
            );

            // 2. Brand
            $brandName = $row['brand'] ?? $row['merk'] ?? 'Generic';
            if (strtolower($brandName) === 'belum di set') $brandName = 'Generic';

            $brand = Brand::firstOrCreate(
                ['nama_brand' => $brandName, 'office_id' => $officeId],
                ['slug' => Str::slug($brandName)]
            );

            // 3. Supplier
            $supplierName = $row['supplier'] ?? $row['pemasok'] ?? 'General Supplier';
            if (strtolower($supplierName) === 'belum di set') $supplierName = 'General Supplier';

            $supplier = Partner::firstOrCreate(
                ['nama' => $supplierName, 'office_id' => $officeId],
                ['tipe_mitra' => 'Supplier', 'status' => 'Active']
            );

            // 4. Unit & Prices
            $unit = $row['satuan'] ?? $row['unit'] ?? 'pcs';
            if (strtolower($unit) === 'belum di set') $unit = 'pcs';
            
            $buyPrice = $this->parseNumber($row['harga_beli'] ?? $row['hpp'] ?? 0);
            $sellPrice = $this->parseNumber($row['harga_jual'] ?? $row['harga'] ?? 0);
            
            // 5. Update/Create Product
            $productData = [
                'nama_produk' => $name,
                'product_category_id' => $category->id,
                'brand_id' => $brand->id,
                'supplier_id' => $supplier->id,
                'satuan' => $unit,
                'harga_beli' => $buyPrice,
                'harga_jual' => $sellPrice,
                'track_stock' => true,
            ];
            
            // To handle COA safely:
            $product = Product::firstOrNew(['sku_kode' => $sku, 'office_id' => $officeId]);
            $product->fill($productData);
            if (!$product->exists && $defaultCoa) {
                 $product->coa_id = $defaultCoa->id;
            } elseif ($product->exists && !$product->coa_id && $defaultCoa) {
                 $product->coa_id = $defaultCoa->id;
            }
            $product->save();

            // 6. Stock Mutation
            $qty = $this->parseNumber($row['stok'] ?? $row['qty'] ?? $row['jumlah'] ?? 0);
            
            if ($qty > 0) {
                // Calculate diff
                $currentStock = $product->qty;
                $diff = $qty - $currentStock;

                if ($diff != 0) {
                    StockMutation::create([
                        'office_id' => $officeId,
                        'product_id' => $product->id,
                        'stock_location_id' => $defaultLocation->id,
                        'type' => $diff > 0 ? 'IN' : 'OUT',
                        'qty' => abs($diff),
                        'remaining_qty' => $diff > 0 ? abs($diff) : 0,
                        'cost_price' => $buyPrice,
                        'notes' => 'Import Stock Adjustment',
                        'reference_type' => 'Import',
                    ]);

                    $product->qty = $qty;
                    $product->save();
                }
            }
        }
    }
        //     if (!$product->exists && $defaultCoa) {
        //          $product->coa_id = $defaultCoa->id;
        //     } elseif ($product->exists && !$product->coa_id && $defaultCoa) {
        //          $product->coa_id = $defaultCoa->id;
        //     }
        //     $product->save();

        //     // 6. Stock Mutation
        //     $qty = $this->parseNumber($row['stok'] ?? $row['qty'] ?? $row['jumlah'] ?? 0);
            
    private function parseNumber($value)
    {
        if (is_numeric($value)) return $value;
        if (is_string($value)) {
            // Handle "=0" case or other excel formula artifacts if they appear as string
            if (strpos($value, '=') === 0) {
                $value = substr($value, 1);
            }
            
            // Remove Rp, dots (thousand separator), replace comma with dot
            $clean = preg_replace('/[^0-9,.-]/', '', $value);
            // Assuming ID format: 1.000,00
            // If it contains comma, replace dot with nothing, comma with dot
            if (strpos($clean, ',') !== false) {
                $clean = str_replace('.', '', $clean);
                $clean = str_replace(',', '.', $clean);
            }
            return (float) $clean;
        }
        return 0;
    }
}
