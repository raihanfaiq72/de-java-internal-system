<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $invoices = DB::table('invoices')->where('status_pembayaran', 'Unpaid')->limit(3000)->get();
        $akunKas = DB::table('chart_of_accounts')->where('is_kas_bank', true)->value('id') ?? 1;

        $data = [];
        foreach ($invoices as $inv) {
            $data[] = [
                'office_id' => 1,
                'invoice_id' => $inv->id,
                'nomor_pembayaran' => 'PYM-' . rand(1000, 9999) . $inv->id,
                'tgl_pembayaran' => now(),
                'metode_pembayaran' => 'Transfer',
                'jumlah_bayar' => $inv->total_akhir,
                'akun_keuangan_id' => $akunKas,
                'created_at' => now()
            ];

            DB::table('invoices')->where('id', $inv->id)->update(['status_pembayaran' => 'Paid']);
        }

        foreach (array_chunk($data, 500) as $chunk) {
            DB::table('payments')->insert($chunk);
        }
    }
}