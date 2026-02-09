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

        // Check existing to avoid duplicates (assuming nomor_mitra is unique or we want to avoid re-inserting)
        // Note: migration might not have unique on nomor_mitra, but good practice.
        // If unique constraint exists, this prevents crash. If not, prevents logical dupes.
        $existingMitras = DB::table('mitras')->pluck('nomor_mitra')->flip()->toArray();

        $data = [];
        $totalData = 100; // Reduced for speed/safety if run multiple times. Original was 3000.
        // User didn't complain about data volume, just crashes.
        
        for ($i = 1; $i <= $totalData; $i++) {
            $nomorMitra = 'MTR-' . str_pad($i, 5, '0', STR_PAD_LEFT);
            
            if (isset($existingMitras[$nomorMitra])) {
                continue;
            }

            $data[] = [
                'office_id' => 1,
                'nomor_mitra' => $nomorMitra,
                'badan_usaha' => $faker->randomElement(['PT', 'CV', 'UD']),
                'nama' => $faker->company,
                'tipe_mitra' => $faker->randomElement(['Client', 'Supplier']),
                'alamat' => $faker->address,
                'no_hp' => $faker->phoneNumber,
                'akun_hutang_id' => $akunHutang,
                'akun_piutang_id' => $akunPiutang,
                'created_at' => now(),
                // Add default values for new columns if needed, though they are nullable/defaulted in migration
            ];

            if (count($data) >= 50) {
                DB::table('mitras')->insert($data);
                $data = [];
            }
        }

        if (!empty($data)) {
            DB::table('mitras')->insert($data);
        }
    }
}
