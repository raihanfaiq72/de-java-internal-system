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
        Schema::create('delivery_order_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('chart_of_accounts_id')
                ->references('id')
                ->on('financial_accounts')
                ->onDelete('restrict');
            $table->decimal('total_cost', 15, 2);

            $table->integer('delivery_queue')->default(1); // stop order

            $table->enum('delivery_status', [
                'pending',
                'delivered',
                'failed',
                'rejected',
                'returned',
            ])->default('pending');

            $table->timestamp('arrived_at')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->string('proof_photo')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_order_invoices');
    }
};
