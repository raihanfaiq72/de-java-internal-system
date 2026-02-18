<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaperIdSeeder extends Seeder
{
    public function run()
    {
        $officeId = 1; // Default office

        $accounts = [
            // Aktiva (1)
            // Kas
            ['1101', 'Kas', 'Aktiva', true],
            // Bank
            ['1201', 'Bank', 'Aktiva', true],
            ['1251', 'Rekening Bersama Digital Payment Paper.id', 'Aktiva', true],
            // Piutang
            ['1301', 'Piutang Usaha', 'Aktiva', false],
            // Persediaan
            ['1501', 'Persediaan', 'Aktiva', false],
            ['1502', 'Persediaan dalam Perjalanan', 'Aktiva', false],
            ['1503', 'Persediaan Konsinyasi', 'Aktiva', false],
            // Pajak Dibayar Dimuka
            ['1701', 'PPN Masukan', 'Aktiva', false],
            ['1702', 'PPH Pasal 23 Dibayar Dimuka', 'Aktiva', false],
            // Aktiva Tetap
            ['1801', 'Bangunan', 'Aktiva', false],
            ['1802', 'Peralatan', 'Aktiva', false],
            ['1803', 'Kendaraan', 'Aktiva', false],
            // Akumulasi Penyusutan
            ['1851', 'Akumulasi Penyusutan Bangunan', 'Aktiva', false],
            ['1852', 'Akumulasi Penyusutan Peralatan', 'Aktiva', false],
            ['1853', 'Akumulasi Penyusutan Kendaraan', 'Aktiva', false],

            // Kewajiban (2)
            // Hutang Usaha
            ['2101', 'Hutang Usaha', 'Kewajiban', false],
            // Hutang Non-Usaha
            ['2201', 'Pendapatan Diterima Di Muka', 'Kewajiban', false],
            ['2203', 'Penjualan dimuka in transit', 'Kewajiban', false],
            // Hutang Pajak
            ['2301', 'Hutang PPH Pasal 21', 'Kewajiban', false],
            ['2302', 'Hutang PPH Pasal 23', 'Kewajiban', false],
            ['2303', 'Hutang PPH Pasal 4(2)', 'Kewajiban', false],
            ['2304', 'Hutang PPH Pasal 25', 'Kewajiban', false],
            ['2305', 'PPN Keluaran', 'Kewajiban', false],
            ['2306', 'Hutang PPN', 'Kewajiban', false],
            // Kewajiban Jangka Panjang
            ['2401', 'Hutang Bank', 'Kewajiban', false],

            // Modal (3)
            ['3101', 'Modal Disetor', 'Modal', false],
            ['3301', 'Dividen', 'Modal', false],
            ['3991', 'Saldo Ekuitas Awal', 'Modal', false],
            ['3201', 'Laba Ditahan', 'Modal', false],
            ['3202', 'Laba Tahun Berjalan', 'Modal', false],
        ];

        foreach ($accounts as $acc) {
            DB::table('chart_of_accounts')->updateOrInsert(
                [
                    'office_id' => $officeId,
                    'kode_akun' => $acc[0],
                ],
                [
                    'nama_akun' => $acc[1],
                    'kelompok_akun' => $acc[2],
                    'is_kas_bank' => $acc[3],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
