<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        // Get Paid invoices that need payments recorded
        $invoices = DB::table('invoices')
            ->where('office_id', 1)
            ->where('status_pembayaran', 'Paid')
            ->get();
        
        $akunKas = DB::table('chart_of_accounts')->where('is_kas_bank', true)->value('id') ?? 1;

        $data = [];

        foreach ($invoices as $inv) {
            // Check if payment already exists for this invoice
            $hasPayment = DB::table('payments')->where('invoice_id', $inv->id)->exists();
            if ($hasPayment) {
                continue;
            }

            $nomorPembayaran = 'PYM-'.str_pad($inv->id, 6, '0', STR_PAD_LEFT);

            $data[] = [
                'office_id' => 1,
                'invoice_id' => $inv->id,
                'nomor_pembayaran' => $nomorPembayaran,
                'tgl_pembayaran' => $inv->updated_at ?: now(),
                'metode_pembayaran' => collect(['Transfer', 'Cash', 'Lainnya'])->random(),
                'jumlah_bayar' => $inv->total_akhir,
                'akun_keuangan_id' => $akunKas,
                'created_at' => now(),
            ];
        }

        if (! empty($data)) {
            DB::table('payments')->insert($data);
        }
    }
}
