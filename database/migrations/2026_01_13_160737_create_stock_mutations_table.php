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
        Schema::create('stock_mutations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->enum('type', ['IN', 'OUT', 'ADJUSTMENT']);
            $table->decimal('qty', 15, 2);
            $table->decimal('remaining_qty', 15, 2)->default(0); // For FIFO (IN only)
            $table->decimal('cost_price', 15, 2)->default(0);    // For valuation
            $table->string('reference_type')->nullable();        // Invoice, Adjustment, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_mutations');
    }
};
