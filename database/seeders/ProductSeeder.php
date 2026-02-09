<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        
        // Ensure dependencies exist
        $categoryId = DB::table('product_categories')->first()->id ?? 1;
        $brandId = DB::table('brands')->first()->id ?? 1;
        $supplierId = DB::table('mitras')->where('tipe_mitra', 'Supplier')->first()->id ?? 1;
        $coaId = DB::table('chart_of_accounts')->where('kode_akun', '1501')->value('id') ?? 1; // Persediaan

        $categories = DB::table('product_categories')->pluck('id')->toArray();
        $brands = DB::table('brands')->pluck('id')->toArray();
        $suppliers = DB::table('mitras')->where('tipe_mitra', 'Supplier')->pluck('id')->toArray();

        if (empty($categories) || empty($brands) || empty($suppliers)) {
            $this->command->error('Categories, Brands, or Suppliers missing. Run their seeders first.');
            return;
        }

        $totalData = 100; // Reduced from 3000 for speed
        
        // Get existing SKUs to avoid duplicates
        $existingSkus = DB::table('products')->pluck('sku_kode')->flip()->toArray();

        $data = [];
        $batchSize = 50;

        for ($i = 1; $i <= $totalData; $i++) {
            $sku = 'PROD-' . str_pad($i, 5, '0', STR_PAD_LEFT);
            
            if (isset($existingSkus[$sku])) {
                continue;
            }

            $data[] = [
                'office_id' => 1,
                'sku_kode' => $sku, 
                'nama_produk' => 'Produk Test ' . $i,
                'product_category_id' => $faker->randomElement($categories),
                'supplier_id' => $faker->randomElement($suppliers),
                'brand_id' => $faker->randomElement($brands),
                'harga_beli' => $faker->numberBetween(5000, 50000),
                'harga_jual' => $faker->numberBetween(60000, 150000),
                'kemasan' => 1,
                'satuan' => 'pcs',
                'track_stock' => true,
                'qty' => $faker->numberBetween(10, 100),
                'coa_id' => $coaId,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($data) >= $batchSize) {
                DB::table('products')->insert($data);
                $data = [];
            }
        }

        if (!empty($data)) {
            DB::table('products')->insert($data);
        }
    }
}
