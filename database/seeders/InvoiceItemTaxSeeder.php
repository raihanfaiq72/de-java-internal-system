<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceItemTaxSeeder extends Seeder
{
    public function run(): void
    {
        $items = DB::table('invoice_items')->limit(3000)->get();
        $tax = DB::table('taxes')->first();

        if ($tax) {
            $data = [];
            foreach ($items as $item) {
                $data[] = [
                    'invoice_item_id' => $item->id,
                    'tax_id' => $tax->id,
                    'nilai_pajak_diterapkan' => ($item->total_harga_item * $tax->persentase) / 100,
                    'created_at' => now()
                ];
            }

            foreach (array_chunk($data, 500) as $chunk) {
                DB::table('invoice_item_taxes')->insert($chunk);
            }
        }
    }
}