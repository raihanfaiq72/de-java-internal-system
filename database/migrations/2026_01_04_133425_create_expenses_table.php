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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('akun_keuangan_id')->constrained('chart_of_accounts');
            $table->string('nama_biaya');
            $table->string('nama_vendor')->nullable();
            $table->foreignId('akun_beban_id')->constrained('chart_of_accounts');
            $table->date('tgl_biaya');
            $table->string('kategori_biaya', 100)->nullable();
            $table->decimal('jumlah', 15, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
