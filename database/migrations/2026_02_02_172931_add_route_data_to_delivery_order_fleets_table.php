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
        Schema::table('delivery_order_fleets', function (Blueprint $table) {
            $table->longText('route_data')->nullable(); // JSON coordinates
            $table->decimal('estimated_distance_km', 10, 2)->nullable();
            $table->decimal('estimated_fuel_cost', 15, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_order_fleets', function (Blueprint $table) {
            $table->dropColumn(['route_data', 'estimated_distance_km', 'estimated_fuel_cost']);
        });
    }
};
