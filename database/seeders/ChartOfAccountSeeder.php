<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChartOfAccountSeeder extends Seeder
{
    public function run()
    {
        $baseAccounts = [
            ['1000', 'Aktiva', 'Aktiva', false],
            ['1101', 'Kas', 'Aktiva', true],
            ['1201', 'Bank', 'Aktiva', true],
            ['1301', 'Piutang Usaha', 'Aktiva', false],
            ['2101', 'Hutang Usaha', 'Kewajiban', false],
            ['3101', 'Modal Disetor', 'Modal', false],
            ['4101', 'Penjualan Umum', 'Pendapatan', false],
            ['5101', 'Harga Pokok Penjualan', 'Beban', false],
            ['6101', 'Beban Gaji', 'Beban', false],
        ];

        $data = [];
        foreach ($baseAccounts as $acc) {
            $data[] = [
                'kode_akun' => $acc[0],
                'nama_akun' => $acc[1],
                'kelompok_akun' => $acc[2],
                'is_kas_bank' => $acc[3],
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        // Tambah 3000 akun dummy untuk test performa
        for ($i = 1; $i <= 3000; $i++) {
            $data[] = [
                'kode_akun' => '9' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'nama_akun' => 'Akun Beban Pengujian ' . $i,
                'kelompok_akun' => 'Beban',
                'is_kas_bank' => false,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        foreach (array_chunk($data, 500) as $chunk) {
            DB::table('chart_of_accounts')->insert($chunk);
        }
    }
}