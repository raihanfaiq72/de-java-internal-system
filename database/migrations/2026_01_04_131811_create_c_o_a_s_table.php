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
            $table->foreignId('tipe_id')->constrained('coa_type')->onDelete('cascade');

            $table->string('kode_akun', 20);
            $table->string('nama_akun', 150);
            $table->boolean('is_kas_bank')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['office_id', 'kode_akun']);
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
