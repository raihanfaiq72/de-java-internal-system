<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained('offices')->onDelete('cascade');
            $table->string('kode_akun', 20)->unique();
            $table->string('nama_akun', 100);
            $table->enum('kelompok_akun', [
                'Aktiva', 'Kewajiban', 'Modal', 'Pendapatan', 'Beban Pokok', 'Beban', 'Beban Penyusutan', 'Pendapatan Lain', 'Beban Lain', 'Beban Pajak'
            ]);
            $table->string('tipe_akun', 100);
            $table->boolean('is_kas_bank')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};