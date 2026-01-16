<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $mitraIds = DB::table('mitras')->pluck('id')->toArray();
        $productIds = DB::table('products')->pluck('id')->toArray();
        
        if (empty($mitraIds) || empty($productIds)) {
            return;
        }

        DB::table('taxes')->updateOrInsert(
            ['office_id' => 1, 'nama_pajak' => 'PPN 11%'], // Cari berdasarkan kantor dan nama
            [
                'persentase' => 11,
                'tipe_pajak' => 'Exclusive',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        $totalData = 3000;
        $batchSize = 500;

        for ($i = 1; $i <= $totalData; $i++) {
            $invId = DB::table('invoices')->insertGetId([
                'office_id' => 1,
                'tipe_invoice' => 'Sales',
                'nomor_invoice' => 'INV/2026/' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'tgl_invoice' => now()->subDays(rand(1, 30)),
                'mitra_id' => $faker->randomElement($mitraIds),
                'status_dok' => 'Draft',
                'status_pembayaran' => 'Unpaid',
                'subtotal' => 100000,
                'total_akhir' => 111000,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::table('invoice_items')->insert([
                'invoice_id' => $invId,
                'produk_id' => $faker->randomElement($productIds),
                'qty' => rand(1, 5),
                'harga_satuan' => 20000,
                'total_harga_item' => 100000,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $akunKas = DB::table('chart_of_accounts')->where('is_kas_bank', true)->value('id') ?? 1;
            $akunBeban = DB::table('chart_of_accounts')->where('kelompok_akun', 'Beban')->value('id') ?? 1;

            if ($i <= 1000) {
                DB::table('expenses')->insert([
                    'office_id' => 1,
                    'akun_keuangan_id' => $akunKas,
                    'nama_biaya' => 'Biaya Operasional ' . $i,
                    'akun_beban_id' => $akunBeban,
                    'tgl_biaya' => now(),
                    'jumlah' => rand(10000, 50000),
                    'kategori_biaya' => 'Lain-lain',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
}