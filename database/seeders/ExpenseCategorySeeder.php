<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Operasional Kantor',
            'Gaji & Tunjangan',
            'Pemasaran & Iklan',
            'Transportasi & Perjalanan',
            'Utilitas (Listrik, Air, Internet)',
            'Pemeliharaan & Perbaikan',
            'Sewa',
            'Pajak & Perizinan',
            'Lain-lain',
        ];

        foreach ($categories as $cat) {
            DB::table('expense_categories')->updateOrInsert(
                ['name' => $cat, 'office_id' => 1],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
