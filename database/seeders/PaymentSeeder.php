<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $invoice = DB::table('invoices')->where('tipe_invoice', 'Sales')->first();
        $akunKas = DB::table('chart_of_accounts')->where('is_kas_bank', true)->first();

        if ($invoice && $akunKas) {
            DB::table('payments')->insert([
                'invoice_id' => $invoice->id,
                'nomor_pembayaran' => 'PYI/2026/0001',
                'ref_no' => 'TRF-99283',
                'tgl_pembayaran' => now(),
                'metode_pembayaran' => 'Transfer',
                'jumlah_bayar' => $invoice->total_akhir,
                'akun_keuangan_id' => $akunKas->id,
                'catatan' => 'Pembayaran lunas untuk invoice pertama.',
                'created_at' => now()
            ]);

            DB::table('invoices')
                ->where('id', $invoice->id)
                ->update(['status_pembayaran' => 'Paid']);
        }
    }
}
