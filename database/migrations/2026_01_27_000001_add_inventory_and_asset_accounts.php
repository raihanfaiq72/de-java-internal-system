<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $offices = DB::table('offices')->pluck('id');
        if ($offices->isEmpty()) {
            return; // Fallback if no office table or empty
        }

        $newAccounts = [
            ['1401', 'Persediaan Barang', 'Aktiva', false],
            ['1501', 'Aset Tetap', 'Aktiva', false],
            ['1502', 'Akumulasi Penyusutan', 'Aktiva', false], // Contra-asset
            ['6201', 'Beban Penyusutan', 'Beban', false],
        ];

        foreach ($offices as $officeId) {
            foreach ($newAccounts as $acc) {
                $exists = DB::table('chart_of_accounts')
                    ->where('office_id', $officeId)
                    ->where('kode_akun', $acc[0])
                    ->exists();
                
                if (!$exists) {
                    DB::table('chart_of_accounts')->insert([
                        'office_id' => $officeId,
                        'kode_akun' => $acc[0],
                        'nama_akun' => $acc[1],
                        'kelompok_akun' => $acc[2],
                        'is_kas_bank' => $acc[3],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optional: delete added accounts
    }
};