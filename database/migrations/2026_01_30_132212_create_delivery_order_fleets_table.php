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
        Schema::create('delivery_order_fleets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fleet_id')->constrained('fleets');
            $table->foreignId('driver_id')->nullable()->constrained('users');

            // Fuel tracking
            $table->decimal('fuel_start_liters', 8, 2)->nullable();
            $table->decimal('fuel_end_liters', 8, 2)->nullable();
            $table->decimal('distance_traveled_km', 8, 2)->nullable();
            $table->decimal('fuel_used_liters', 8, 2)->nullable();

            // Additional operational costs
            $table->decimal('toll_cost', 12, 2)->default(0);
            $table->decimal('parking_cost', 12, 2)->default(0);
            $table->decimal('other_cost', 12, 2)->default(0);

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_order_fleets');
    }
};
