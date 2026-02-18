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
        Schema::create('coa_group', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained('offices')->onDelete('cascade');
            $table->string('kode_kelompok', 10);
            $table->string('nama_kelompok', 100);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['office_id', 'kode_kelompok']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coa_group');
    }
};
