<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $mitraIds = DB::table('mitras')->pluck('id')->toArray();
        $productIds = DB::table('products')->pluck('id')->toArray();

        if (empty($mitraIds) || empty($productIds)) {
            $this->command->warn('Mitras or Products missing. Skipping transactions.');

            return;
        }

        // Ensure Tax Exists
        DB::table('taxes')->updateOrInsert(
            ['office_id' => 1, 'nama_pajak' => 'PPN 11%'],
            [
                'persentase' => 11,
                'tipe_pajak' => 'Exclusive',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $totalData = 100;

        // Check existing invoices
        $existingInvoices = DB::table('invoices')->pluck('nomor_invoice')->flip()->toArray();

        for ($i = 1; $i <= $totalData; $i++) {
            $nomorInvoice = 'INV/2026/'.str_pad($i, 5, '0', STR_PAD_LEFT);

            if (isset($existingInvoices[$nomorInvoice])) {
                continue;
            }

            $invId = DB::table('invoices')->insertGetId([
                'office_id' => 1,
                'tipe_invoice' => 'Sales',
                'nomor_invoice' => $nomorInvoice,
                'tgl_invoice' => now()->subDays(rand(1, 30)),
                'mitra_id' => $faker->randomElement($mitraIds),
                'status_dok' => 'Draft',
                'status_pembayaran' => 'Unpaid',
                'subtotal' => 100000,
                'total_akhir' => 111000,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('invoice_items')->insert([
                'invoice_id' => $invId,
                'produk_id' => $faker->randomElement($productIds),
                'qty' => rand(1, 5),
                'harga_satuan' => 20000,
                'total_harga_item' => 100000,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Expenses (Demo data)
            $akunKas = DB::table('chart_of_accounts')->where('is_kas_bank', true)->value('id') ?? 1;

            $akunBeban = DB::table('chart_of_accounts')
                ->join('coa_type', 'chart_of_accounts.tipe_id', '=', 'coa_type.id')
                ->join('coa_group', 'coa_type.kelompok_id', '=', 'coa_group.id')
                ->where('coa_group.nama_kelompok', 'like', '%Beban%')
                ->select('chart_of_accounts.id')
                ->first()->id ?? 1;

            if ($i <= 20) { // Limit expenses
                // Check if expense exists (simple check by name/date/amount or just always add? expenses usually don't have unique code in seeder)
                // We'll skip adding expenses if invoice existed, which we already do by 'continue'.
                // If invoice didn't exist, we add expense.

                DB::table('expenses')->insert([
                    'office_id' => 1,
                    'akun_keuangan_id' => $akunKas,
                    'nama_biaya' => 'Biaya Operasional '.$i,
                    'nama_vendor' => 'Vendor '.$i,
                    'akun_beban_id' => $akunBeban,
                    'tgl_biaya' => now(),
                    'jumlah' => rand(10000, 50000),
                    'kategori_biaya' => 'Operasional Kantor',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
