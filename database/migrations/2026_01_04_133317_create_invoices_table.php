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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained('offices')->onDelete('cascade');
            $table->enum('tipe_invoice', ['Sales', 'Purchase']);
            $table->string('logo_img')->nullable();
            $table->string('nomor_invoice', 100)->unique();

            $table->date('tgl_invoice');
            $table->date('tgl_jatuh_tempo')->nullable();
            $table->string('ref_no', 100)->nullable();

            $table->foreignId('mitra_id')->constrained('mitras');
            $table->bigInteger('kontak_person_id')->nullable();
            $table->bigInteger('sales_id')->nullable();

            $table->enum('status_dok', ['Draft','Sent','Failed','Approved','Rejected'])->default('Draft');
            $table->enum('status_pembayaran', ['Draft','Unpaid','Overdue','Paid','Partially Paid'])->default('Unpaid');
            $table->enum('status_perjalanan', ['Cetak','Terkirim','Diterima'])->default('Cetak');

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total_diskon_item', 15, 2)->default(0);
            $table->decimal('diskon_tambahan_nilai', 15, 2)->default(0);
            $table->enum('diskon_tambahan_tipe', ['Fixed','Percentage'])->default('Fixed');
            $table->decimal('biaya_kirim', 15, 2)->default(0);
            $table->decimal('uang_muka', 15, 2)->default(0);
            $table->decimal('total_akhir', 15, 2)->default(0);

            $table->text('keterangan')->nullable();
            $table->text('syarat_ketentuan')->nullable();
            $table->boolean('perlu_acc_admin')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};