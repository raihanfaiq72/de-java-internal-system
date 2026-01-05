<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class MitraSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $akunHutang = DB::table('chart_of_accounts')->where('kode_akun', '2101')->value('id') ?? 1;
        $akunPiutang = DB::table('chart_of_accounts')->where('kode_akun', '1301')->value('id') ?? 1;

        $data = [];
        for ($i = 1; $i <= 3000; $i++) {
            $data[] = [
                'nomor_mitra' => 'MTR-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'badan_usaha' => $faker->randomElement(['PT', 'CV', 'UD']),
                'nama' => $faker->company,
                'tipe_mitra' => $faker->randomElement(['Client', 'Supplier']),
                'alamat' => $faker->address,
                'no_hp' => $faker->phoneNumber,
                'akun_hutang_id' => $akunHutang,
                'akun_piutang_id' => $akunPiutang,
                'created_at' => now()
            ];

            if ($i % 500 == 0) {
                DB::table('mitras')->insert($data);
                $data = [];
            }
        }
    }
}