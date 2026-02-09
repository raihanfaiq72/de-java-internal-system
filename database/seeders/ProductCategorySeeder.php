<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Smartphone',
            'Laptop',
            'Tablet',
            'Aksesoris',
            'Komputer',
            'Printer',
            'Tinta',
            'Networking',
            'Sparepart',
            'Jasa Service',
        ];

        foreach ($categories as $cat) {
            DB::table('product_categories')->updateOrInsert(
                ['nama_kategori' => $cat, 'office_id' => 1],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
