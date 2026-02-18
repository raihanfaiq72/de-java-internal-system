<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MitraSeeder extends Seeder
{
    private function fakeIndoAddress($faker, &$cityData)
    {
        $streets = ['Melati', 'Kenanga', 'Mawar', 'Anggrek', 'Flamboyan', 'Diponegoro', 'Sudirman', 'Gatot Subroto'];

        // Kota + provinsi + range koordinat biar realistis
        $cities = [
            [
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'lat_min' => -6.30,
                'lat_max' => -6.20,
                'lng_min' => 106.75,
                'lng_max' => 106.85,
            ],
            [
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'lat_min' => -6.95,
                'lat_max' => -6.85,
                'lng_min' => 107.55,
                'lng_max' => 107.70,
            ],
            [
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
                'lat_min' => -7.35,
                'lat_max' => -7.20,
                'lng_min' => 112.65,
                'lng_max' => 112.80,
            ],
            [
                'city' => 'Semarang',
                'province' => 'Jawa Tengah',
                'lat_min' => -7.05,
                'lat_max' => -6.95,
                'lng_min' => 110.35,
                'lng_max' => 110.50,
            ],
            [
                'city' => 'Yogyakarta',
                'province' => 'DI Yogyakarta',
                'lat_min' => -7.85,
                'lat_max' => -7.75,
                'lng_min' => 110.35,
                'lng_max' => 110.45,
            ],
            [
                'city' => 'Medan',
                'province' => 'Sumatera Utara',
                'lat_min' => 3.55,
                'lat_max' => 3.65,
                'lng_min' => 98.60,
                'lng_max' => 98.70,
            ],
            [
                'city' => 'Denpasar',
                'province' => 'Bali',
                'lat_min' => -8.70,
                'lat_max' => -8.60,
                'lng_min' => 115.15,
                'lng_max' => 115.25,
            ],
        ];

        $cityData = $faker->randomElement($cities);

        $address = "Jl. {$faker->randomElement($streets)} No. {$faker->buildingNumber}, "
            ."Kel. {$faker->citySuffix}, "
            ."Kec. {$faker->lastName}, "
            ."{$cityData['city']}, {$cityData['province']}, Indonesia";

        return $address;
    }

    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $akunHutang = DB::table('chart_of_accounts')->where('kode_akun', '2101')->value('id') ?? 1;
        $akunPiutang = DB::table('chart_of_accounts')->where('kode_akun', '1301')->value('id') ?? 1;

        $existingMitras = DB::table('mitras')->pluck('nomor_mitra')->flip()->toArray();

        $data = [];
        $totalData = 100;

        for ($i = 1; $i <= $totalData; $i++) {
            $nomorMitra = 'MTR-'.str_pad($i, 5, '0', STR_PAD_LEFT);

            if (isset($existingMitras[$nomorMitra])) {
                continue;
            }

            // Generate alamat + ambil data kota untuk koordinat
            $cityData = null;
            $alamat = $this->fakeIndoAddress($faker, $cityData);

            $latitude = $faker->randomFloat(7, $cityData['lat_min'], $cityData['lat_max']);
            $longitude = $faker->randomFloat(7, $cityData['lng_min'], $cityData['lng_max']);

            $data[] = [
                'office_id' => 1,
                'nomor_mitra' => $nomorMitra,
                'badan_usaha' => $faker->randomElement(['PT', 'CV', 'UD']),
                'nama' => $faker->company,
                'tipe_mitra' => $faker->randomElement(['Client', 'Supplier']),
                'alamat' => $alamat,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'no_hp' => $faker->phoneNumber,
                'akun_hutang_id' => $akunHutang,
                'akun_piutang_id' => $akunPiutang,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($data) >= 50) {
                DB::table('mitras')->insert($data);
                $data = [];
            }
        }

        if (! empty($data)) {
            DB::table('mitras')->insert($data);
        }
    }
}
