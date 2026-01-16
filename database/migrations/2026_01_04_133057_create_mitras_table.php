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
        Schema::create('mitras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained('offices')->onDelete('cascade');
            $table->string('nomor_mitra', 50)->unique()->nullable();
            $table->string('badan_usaha', 20)->nullable();
            $table->string('nama', 150);
            $table->string('no_hp', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->enum('tipe_mitra', ['Supplier', 'Client', 'Both']);
            $table->text('alamat')->nullable();

            $table->string('kontak_nama', 100)->nullable();
            $table->string('kontak_jabatan', 100)->nullable();
            $table->string('kontak_no_hp', 20)->nullable();
            $table->string('kontak_email', 100)->nullable();

            $table->foreignId('akun_hutang_id')->nullable()
                ->constrained('chart_of_accounts');

            $table->foreignId('akun_piutang_id')->nullable()
                ->constrained('chart_of_accounts');

            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mitras');
    }
};