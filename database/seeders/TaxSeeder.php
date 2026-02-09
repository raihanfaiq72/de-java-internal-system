<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxSeeder extends Seeder
{
    public function run(): void
    {
        $taxes = [
            ['nama_pajak' => 'PPN 11%', 'persentase' => 11.000, 'tipe_pajak' => 'Exclusive'],
            ['nama_pajak' => 'PPN 12%', 'persentase' => 12.000, 'tipe_pajak' => 'Exclusive'],
            ['nama_pajak' => 'Non PPN', 'persentase' => 0.000, 'tipe_pajak' => 'Exclusive'],
        ];

        foreach ($taxes as $tax) {
            DB::table('taxes')->updateOrInsert(
                ['nama_pajak' => $tax['nama_pajak'], 'office_id' => 1],
                [
                    'persentase' => $tax['persentase'],
                    'tipe_pajak' => $tax['tipe_pajak'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
