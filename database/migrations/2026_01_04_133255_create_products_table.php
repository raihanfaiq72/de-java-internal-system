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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku_kode', 50)->unique();
            $table->string('nama_produk', 255);
            $table->text('deskripsi_produk')->nullable();

            $table->foreignId('product_category_id')
                ->constrained('product_categories');

            $table->foreignId('unit_category_id')
                ->constrained('unit_categories');

            $table->foreignId('unit_id')
                ->constrained('units');

            $table->decimal('harga_beli', 15, 2)->default(0);
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->boolean('track_stock')->default(true);
            $table->integer('qty')->default(0);
            $table->string('foto_produk')->nullable();

            $table->foreignId('akun_penjualan_id')
                ->constrained('chart_of_accounts');

            $table->foreignId('akun_pembelian_id')
                ->constrained('chart_of_accounts');

            $table->foreignId('akun_diskon_penjualan_id')
                ->constrained('chart_of_accounts');

            $table->foreignId('akun_diskon_pembelian_id')
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
        Schema::dropIfExists('products');
    }
};
