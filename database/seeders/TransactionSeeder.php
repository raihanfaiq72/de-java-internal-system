<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taxId = DB::table('taxes')->insertGetId([
            'nama_pajak' => 'PPN 11%',
            'persentase' => 11.0,
            'tipe_pajak' => 'Exclusive'
        ]);

        $mitraId = DB::table('mitras')->first()->id;

        $invId = DB::table('invoices')->insertGetId([
            'tipe_invoice' => 'Sales',
            'nomor_invoice' => 'INV/2026/001',
            'tgl_invoice' => now(),
            'mitra_id' => $mitraId,
            'status_pembayaran' => 'Unpaid',
            'total_akhir' => 12500000
        ]);

        $prodId = DB::table('products')->first()->id;
        DB::table('invoice_items')->insert([
            'invoice_id' => $invId,
            'produk_id' => $prodId,
            'qty' => 1,
            'harga_satuan' => 12500000,
            'total_harga_item' => 12500000
        ]);

        $akunKas = DB::table('chart_of_accounts')->where('is_kas_bank', true)->value('id') ?? 1;
        
        DB::table('expenses')->insert([
            'akun_keuangan_id' => $akunKas,
            'nama_biaya' => 'Bensin Operasional',
            'akun_beban_id' => $akunKas,
            'tgl_biaya' => now(),
            'jumlah' => 50000,
            'kategori_biaya' => 'Transportasi'
        ]);
    }
}
