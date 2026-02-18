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
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained()->cascadeOnDelete();

            $table->string('delivery_order_number')->unique();
            $table->date('delivery_date');

            $table->enum('status', [
                'draft',
                'scheduled',
                'in_transit',
                'delivering',
                'partially_delivered',
                'completed',
                'returned',
                'cancelled',
            ])->default('draft');

            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('departed_at')->nullable();
            $table->timestamp('returned_at')->nullable();

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
        Schema::dropIfExists('delivery_orders');
    }
};
