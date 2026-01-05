<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class AccountingLogSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $akunId = DB::table('chart_of_accounts')->first()->id ?? 1;

        $journals = [];
        $logs = [];
        
        for ($i = 1; $i <= 3000; $i++) {
            $journals[] = [
                'tgl_jurnal' => now(),
                'keterangan' => 'Jurnal Otomatis Sistem ' . $i,
                'created_at' => now()
            ];

            $logs[] = [
                'user_id' => 1,
                'tindakan' => 'Create',
                'tabel_terkait' => 'invoices',
                'data_id' => $i,
                'ip_address' => $faker->ipv4,
                'created_at' => now()
            ];
        }

        foreach (array_chunk($journals, 500) as $chunk) DB::table('journals')->insert($chunk);
        foreach (array_chunk($logs, 500) as $chunk) DB::table('activity_logs')->insert($chunk);
    }
}