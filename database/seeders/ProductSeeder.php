<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catId = DB::table('unit_categories')->insertGetId([
            'nama_kategori' => 'Satuan',
            'konversi_nilai' => 1.0
        ]);

        $unitId = DB::table('units')->insertGetId([
            'unit_category_id' => $catId,
            'nama_unit' => 'Pcs',
            'simbol' => 'Pcs'
        ]);

        $prodCatId = DB::table('product_categories')->insertGetId([
            'nama_kategori' => 'Elektronik',
            'deskripsi' => 'Kategori barang elektronik'
        ]);

        $akunId = DB::table('chart_of_accounts')->first()->id ?? 1;
        $akunDiskonPenjualanId = $akunId;
        $akunDiskonPembelianId = $akunId;

        DB::table('products')->insert([
            'sku_kode' => 'PROD-001',
            'nama_produk' => 'Laptop Pro 14',
            'product_category_id' => $prodCatId,
            'unit_category_id' => $catId,
            'unit_id' => $unitId,
            'harga_beli' => 10000000,
            'harga_jual' => 12500000,
            'akun_penjualan_id' => $akunId,
            'akun_pembelian_id' => $akunId,
            'akun_diskon_penjualan_id' => $akunDiskonPenjualanId, 
            'akun_diskon_pembelian_id' => $akunDiskonPembelianId,   
        ]);
    }
}
