<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinancialAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            [
                'name' => 'Kas Besar',
                'code' => '1101',
                'type' => 'Cash',
                'description' => 'Kas tunai utama kantor',
                'bank_name' => null,
                'bank_account_number' => null,
                'bank_account_name' => null,
            ],
            [
                'name' => 'Bank BCA',
                'code' => '1201',
                'type' => 'Bank',
                'description' => 'Rekening Operasional BCA',
                'bank_name' => 'BCA',
                'bank_account_number' => '1234567890',
                'bank_account_name' => 'PT DE JAVA',
            ],
            [
                'name' => 'Bank Mandiri',
                'code' => '1201', // Same COA parent
                'type' => 'Bank',
                'description' => 'Rekening Cadangan Mandiri',
                'bank_name' => 'Mandiri',
                'bank_account_number' => '0987654321',
                'bank_account_name' => 'PT DE JAVA',
            ],
        ];

        foreach ($accounts as $acc) {
            DB::table('financial_accounts')->updateOrInsert(
                ['name' => $acc['name'], 'office_id' => 1],
                [
                    'code' => $acc['code'],
                    'type' => $acc['type'],
                    'description' => $acc['description'],
                    'bank_name' => $acc['bank_name'],
                    'bank_account_number' => $acc['bank_account_number'],
                    'bank_account_name' => $acc['bank_account_name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
