<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\Partner;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StockLocation;
use App\Models\StockMutation;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

use App\Models\COA;

use App\Models\User;
use App\Notifications\SystemNotification;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\ImportFailed;

class StockImport implements ToCollection, WithHeadingRow, ShouldQueue, WithChunkReading, WithEvents
{
    protected $officeId;
    protected $userId;
    protected $processedCount = 0;
    public static $mandatoryHeaders = ['sku', 'nama_produk', 'kategori'];

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
                        'Import Stock Selesai',
                        "Proses import data stok telah berhasil diselesaikan. {$this->processedCount} data dimasukkan.",
                        route('import.index'),
                        'success'
                    ));
                }
            },
            ImportFailed::class => function(ImportFailed $event) {
                $user = User::find($this->userId);
                if ($user) {
                    $user->notify(new SystemNotification(
                        'Import Stock Gagal',
                        'Terjadi kesalahan saat import data stok: ' . $event->getException()->getMessage(),
                        route('import.index'),
                        'error'
                    ));
                }
                Log::error('StockImport Failed Event: ' . $event->getException()->getMessage());
            },
        ];
    }

    public function failed(\Throwable $e)
    {
        $user = User::find($this->userId);
        if ($user) {
            $user->notify(new SystemNotification(
                'Import Stock Gagal',
                'Job import gagal diproses: ' . $e->getMessage(),
                route('import.index'),
                'error'
            ));
        }
        Log::error('StockImport Job Failed: ' . $e->getMessage());
    }

    public function collection(Collection $rows)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        try {
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

            // 1. Validate Header (Strict Check)
            if ($rows->isEmpty()) return;
            $firstRow = $rows->first();
            $requiredColumns = ['sku', 'nama_produk', 'kategori'];
            foreach ($requiredColumns as $col) {
                if (!isset($firstRow[$col]) && !array_key_exists($col, $firstRow->toArray())) {
                    throw new \Exception("Template tidak sesuai. Kolom wajib '$col' tidak ditemukan di tab Stok.");
                }
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
                $categoryName = $row['kategori'] ?? $row['category'] ?? 'TANPA KATEGORI';
                if (strtolower($categoryName) === 'belum di set') $categoryName = 'TANPA KATEGORI';
                
                $category = ProductCategory::firstOrCreate(
                    ['nama_kategori' => $categoryName, 'office_id' => $officeId],
                    ['slug' => Str::slug($categoryName)]
                );

                // 2. Brand
                $brandName = $row['brand'] ?? $row['merk'] ?? 'TANPA BRAND';
                if (strtolower($brandName) === 'belum di set') $brandName = 'TANPA BRAND';

                $brand = Brand::firstOrCreate(
                    ['nama_brand' => $brandName, 'office_id' => $officeId],
                    ['slug' => Str::slug($brandName)]
                );

                // 3. Supplier
                $supplierName = $row['supplier'] ?? $row['pemasok'] ?? 'TANPA SUPLIER';
                if (strtolower($supplierName) === 'belum di set') $supplierName = 'TANPA SUPLIER';

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
                    'coa_id' => $defaultCoa ? $defaultCoa->id : null,
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
                $this->processedCount++;

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
        } catch (\Throwable $e) {
            Log::error('StockImport Error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
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
