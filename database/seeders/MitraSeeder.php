<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MitraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $akunHutang = DB::table('chart_of_accounts')->where('kode_akun', 'like', '2%')->value('id') ?? 1;
        $akunPiutang = DB::table('chart_of_accounts')->where('kode_akun', 'like', '1%')->value('id') ?? 1;

        DB::table('mitras')->insert([
            [
                'nomor_mitra' => 'MTR-001',
                'badan_usaha' => 'PT',
                'nama' => 'Global Teknologi Nusantara',
                'tipe_mitra' => 'Client',
                'akun_hutang_id' => $akunHutang,
                'akun_piutang_id' => $akunPiutang,
            ],
            [
                'nomor_mitra' => 'MTR-002',
                'badan_usaha' => 'CV',
                'nama' => 'Sumber Makmur Jaya',
                'tipe_mitra' => 'Supplier',
                'akun_hutang_id' => $akunHutang,
                'akun_piutang_id' => $akunPiutang,
            ]
        ]);
    }
}
