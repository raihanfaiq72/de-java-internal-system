<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $officeId = 1;

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

        $brandIds = [];

        // 1️⃣ Insert brand & simpan ID
        foreach ($brands as $brand) {
            DB::table('brands')->updateOrInsert(
                [
                    'nama_brand' => $brand,
                    'office_id'  => $officeId
                ],
                [
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            $brandIds[] = DB::table('brands')
                ->where('nama_brand', $brand)
                ->where('office_id', $officeId)
                ->value('id');
        }

        // 2️⃣ Ambil semua supplier (mitra tipe Supplier)
        $supplierIds = DB::table('mitras')
            ->where('office_id', $officeId)
            ->where('tipe_mitra', 'Supplier')
            ->pluck('id');

        // 3️⃣ Hubungkan supplier ↔ brand
        foreach ($supplierIds as $supplierId) {
            // tiap supplier kita kasih beberapa brand random
            $randomBrands = collect($brandIds)->random(rand(3, 6));

            foreach ($randomBrands as $brandId) {
                DB::table('supplier_brands')->updateOrInsert(
                    [
                        'office_id'   => $officeId,
                        'supplier_id' => $supplierId,
                        'brand_id'    => $brandId,
                    ],
                    [
                        'updated_at' => now(),
                        'created_at' => now(),
                        'deleted_at' => null,
                    ]
                );
            }
        }
    }
}