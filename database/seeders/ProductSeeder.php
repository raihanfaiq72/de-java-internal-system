<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        // Ambil atau buat ID yang diperlukan
        $prodCatId = DB::table('product_categories')->insertGetId(['nama_kategori' => 'General', 'deskripsi' => 'Testing', 'office_id' => 1]);
        $akunId = DB::table('chart_of_accounts')->first()->id ?? 1;

        $totalData = 3000;
        $batchSize = 500;
        $data = [];

        for ($i = 1; $i <= $totalData; $i++) {
            $data[] = [
                // MENGGUNAKAN PAD UNTUK MENJAMIN KEUNIKAN: PROD-00001, PROD-00002, dst.
                'office_id' => 1,
                'sku_kode' => 'PROD-' . str_pad($i, 5, '0', STR_PAD_LEFT), 
                'nama_produk' => 'Produk Test ' . $i,
                'product_category_id' => $prodCatId,
                'harga_beli' => $faker->numberBetween(5000, 50000),
                'harga_jual' => $faker->numberBetween(60000, 150000),
                'qty' => $faker->numberBetween(100, 500),
                'akun_penjualan_id' => $akunId,
                'akun_pembelian_id' => $akunId,
                'akun_diskon_penjualan_id' => $akunId,
                'akun_diskon_pembelian_id' => $akunId,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Masukkan data per batch agar hemat memori
            if ($i % $batchSize == 0) {
                DB::table('products')->insert($data);
                $data = []; // Reset array setelah insert
            }
        }

        // Masukkan sisa data jika totalData tidak habis dibagi batchSize
        if (!empty($data)) {
            DB::table('products')->insert($data);
        }
    }
}