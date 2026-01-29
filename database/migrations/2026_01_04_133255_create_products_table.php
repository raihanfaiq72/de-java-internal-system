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
            $table->foreignId('office_id')->constrained('offices')->onDelete('cascade');
            $table->string('sku_kode', 50)->unique();
            $table->string('nama_produk', 255);

            $table->foreignId('product_category_id')
                ->constrained('product_categories');

            $table->decimal('harga_beli', 15, 2)->default(0);
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->boolean('track_stock')->default(true);
            $table->integer('qty')->default(0);
            $table->string('foto_produk')->nullable();

            $table->foreignId('coa_id')
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