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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained('offices')->onDelete('cascade');
            $table->foreignId('invoice_id')->constrained('invoices');
            $table->string('nomor_pembayaran', 100)->unique();
            $table->string('ref_no', 100)->nullable();
            $table->date('tgl_pembayaran');
            $table->enum('metode_pembayaran', ['Cash','Transfer','Lainnya']);
            $table->decimal('jumlah_bayar', 15, 2);

            $table->foreignId('akun_keuangan_id')
                ->constrained('chart_of_accounts');

            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};