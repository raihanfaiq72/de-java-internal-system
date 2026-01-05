<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceItemTaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $item = DB::table('invoice_items')->first();
        $tax = DB::table('taxes')->first();

        if ($item && $tax) {
            $nilaiPajak = ($item->total_harga_item * $tax->persentase) / 100;

            DB::table('invoice_item_taxes')->insert([
                'invoice_item_id' => $item->id,
                'tax_id' => $tax->id,
                'nilai_pajak_diterapkan' => $nilaiPajak
            ]);
        }
    }
}
