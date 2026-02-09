<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FleetSeeder extends Seeder
{
    public function run(): void
    {
        $fleets = [
            [
                'fleet_name' => 'Grand Max Blind Van',
                'fuel_type' => 'Pertalite',
                'license_plate' => 'H 1234 AB',
                'km_per_liter' => 10.00,
                'liter_price' => 10000,
            ],
            [
                'fleet_name' => 'L300 Box',
                'fuel_type' => 'Solar',
                'license_plate' => 'H 5678 CD',
                'km_per_liter' => 8.00,
                'liter_price' => 6800,
            ],
            [
                'fleet_name' => 'Truk Engkel',
                'fuel_type' => 'Dexlite',
                'license_plate' => 'H 9012 EF',
                'km_per_liter' => 6.00,
                'liter_price' => 14550,
            ],
        ];

        foreach ($fleets as $fleet) {
            DB::table('fleets')->updateOrInsert(
                ['license_plate' => $fleet['license_plate'], 'office_id' => 1],
                [
                    'fleet_name' => $fleet['fleet_name'],
                    'fuel_type' => $fleet['fuel_type'],
                    'km_per_liter' => $fleet['km_per_liter'],
                    'liter_price' => $fleet['liter_price'] ?? 0, // Handle optional if migration not run yet, but user said match migration
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
