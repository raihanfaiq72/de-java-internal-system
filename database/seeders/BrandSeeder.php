<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            'Samsung',
            'Apple',
            'Xiaomi',
            'Oppo',
            'Vivo',
            'Realme',
            'Infinix',
            'Tecno',
            'Itel',
            'Asus',
            'Lenovo',
            'Dell',
            'HP',
            'Acer',
            'Logitech',
            'Canon',
            'Epson',
            'Generic',
        ];

        foreach ($brands as $brand) {
            DB::table('brands')->updateOrInsert(
                ['nama_brand' => $brand, 'office_id' => 1],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
