<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $invoices = DB::table('invoices')->where('status_pembayaran', 'Unpaid')->limit(100)->get();
        $akunKas = DB::table('chart_of_accounts')->where('is_kas_bank', true)->value('id') ?? 1;

        // Check existing payments
        $existingPayments = DB::table('payments')->pluck('nomor_pembayaran')->flip()->toArray();

        $data = [];
        $updates = [];

        foreach ($invoices as $inv) {
            $nomorPembayaran = 'PYM-' . rand(1000, 9999) . $inv->id;
            
            // Very unlikely to collide with rand, but good to check or just insert.
            // Since rand changes every run, we might add duplicate payments for same invoice if we run seeder multiple times?
            // Yes. Ideally we should check if invoice already has payment.
            
            // Check if this invoice already has a payment linked (if relationship exists, but here we just check payments table)
            // But payments table usually has invoice_id.
            
            $hasPayment = DB::table('payments')->where('invoice_id', $inv->id)->exists();
            if ($hasPayment) {
                continue;
            }

            $data[] = [
                'office_id' => 1,
                'invoice_id' => $inv->id,
                'nomor_pembayaran' => $nomorPembayaran,
                'tgl_pembayaran' => now(),
                'metode_pembayaran' => 'Transfer',
                'jumlah_bayar' => $inv->total_akhir,
                'akun_keuangan_id' => $akunKas,
                'created_at' => now()
            ];

            $updates[] = $inv->id;
        }

        if (!empty($data)) {
            DB::table('payments')->insert($data);
            DB::table('invoices')->whereIn('id', $updates)->update(['status_pembayaran' => 'Paid']);
        }
    }
}
