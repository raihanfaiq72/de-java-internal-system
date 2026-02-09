<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockLocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['name' => 'Gudang Utama', 'type' => 'stock'],
            ['name' => 'Gudang Display', 'type' => 'stock'],
            ['name' => 'Gudang Rusak/Retur', 'type' => 'stock'],
        ];

        foreach ($locations as $loc) {
            DB::table('stock_locations')->updateOrInsert(
                ['name' => $loc['name'], 'office_id' => 1],
                [
                    'type' => $loc['type'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
