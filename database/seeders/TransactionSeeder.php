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
        $stockLocationId = DB::table('stock_locations')->first()->id ?? 1;

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

        $totalData = 50;

        // Check existing invoices
        $existingInvoices = DB::table('invoices')->pluck('nomor_invoice')->flip()->toArray();

        for ($i = 1; $i <= $totalData; $i++) {
            $nomorInvoice = 'INV/2026/'.str_pad($i, 5, '0', STR_PAD_LEFT);

            if (isset($existingInvoices[$nomorInvoice])) {
                continue;
            }

            // Mix of Approved and Draft invoices, with mixed payment status
            $isApproved = $i <= 40; // First 40 are Approved
            $isPaid = $i <= 25;     // First 25 are Paid
            // So: 1-25 Approved+Paid, 26-40 Approved+Unpaid, 41-50 Draft
            
            $subtotal = rand(500000, 5000000);
            $tax = (int)($subtotal * 0.11);
            $totalAkhir = $subtotal + $tax;

            $invId = DB::table('invoices')->insertGetId([
                'office_id' => 1,
                'tipe_invoice' => 'Sales',
                'nomor_invoice' => $nomorInvoice,
                'tgl_invoice' => now()->subDays(rand(1, 30)),
                'tgl_jatuh_tempo' => now()->addDays(rand(7, 30)),
                'mitra_id' => $faker->randomElement($mitraIds),
                'status_dok' => $isApproved ? 'Approved' : 'Draft',
                'status_pembayaran' => $isPaid ? 'Paid' : 'Unpaid',
                'subtotal' => $subtotal,
                'total_akhir' => $totalAkhir,
                'created_at' => now()->subDays(rand(0, 30)),
                'updated_at' => now(),
            ]);

            // Create invoice items with variable quantities
            $qty = rand(2, 10);
            $itemPrice = rand(50000, 500000);
            $itemTotal = $qty * $itemPrice;

            $productId = $faker->randomElement($productIds);
            DB::table('invoice_items')->insert([
                'invoice_id' => $invId,
                'produk_id' => $productId,
                'qty' => $qty,
                'harga_satuan' => $itemPrice,
                'total_harga_item' => $itemTotal,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create stock mutations for inventory activity
            if ($isApproved) {
                DB::table('stock_mutations')->insert([
                    'office_id' => 1,
                    'product_id' => $productId,
                    'type' => 'out',
                    'qty' => $qty,
                    'stock_location_id' => $stockLocationId,
                    'notes' => 'Keluar - Invoice '.$nomorInvoice,
                    'created_at' => now()->subDays(rand(0, 30)),
                    'updated_at' => now(),
                ]);
            }

            // Expenses (Demo data)
            $akunKas = DB::table('chart_of_accounts')->where('is_kas_bank', true)->value('id') ?? 1;

            $akunBeban = DB::table('chart_of_accounts')
                ->join('coa_type', 'chart_of_accounts.tipe_id', '=', 'coa_type.id')
                ->join('coa_group', 'coa_type.kelompok_id', '=', 'coa_group.id')
                ->where('coa_group.kode_kelompok', '>=', 6000)
                ->select('chart_of_accounts.id')
                ->first();
            
            $akunBebanId = $akunBeban ? $akunBeban->id : 1;

            if ($i <= 20) { // Create expenses for first 20 items
                DB::table('expenses')->insert([
                    'office_id' => 1,
                    'akun_keuangan_id' => $akunKas,
                    'nama_biaya' => 'Biaya Operasional '.$i,
                    'nama_vendor' => 'Vendor '.$i,
                    'akun_beban_id' => $akunBebanId,
                    'tgl_biaya' => now()->subDays(rand(0, 30)),
                    'jumlah' => rand(100000, 500000),
                    'kategori_biaya' => 'Operasional Kantor',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
