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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices');
            $table->foreignId('produk_id')->nullable()->constrained('products');

            $table->string('nama_produk_manual')->nullable();
            $table->text('deskripsi_produk')->nullable();

            $table->decimal('qty', 15, 2);
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('diskon_nilai', 15, 2)->default(0);
            $table->decimal('total_harga_item', 15, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};