<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountingLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $journalId = DB::table('journals')->insertGetId([
            'tgl_jurnal' => now(),
            'keterangan' => 'Saldo Awal'
        ]);

        $akunId = DB::table('chart_of_accounts')->first()->id ?? 1;

        DB::table('journal_details')->insert([
            ['journal_id' => $journalId, 'akun_id' => $akunId, 'debit' => 1000000, 'kredit' => 0],
            ['journal_id' => $journalId, 'akun_id' => $akunId, 'debit' => 0, 'kredit' => 1000000]
        ]);

        DB::table('sales_attendances')->insert([
            'user_id' => 1,
            'tgl_presensi' => now(),
            'jam_masuk' => '08:00:00',
            'lokasi_gps' => '-6.2000, 106.8166'
        ]);

        DB::table('activity_logs')->insert([
            'user_id' => 1,
            'tindakan' => 'Create',
            'tabel_terkait' => 'invoices',
            'data_id' => 1,
            'ip_address' => '127.0.0.1'
        ]);
    }
}
