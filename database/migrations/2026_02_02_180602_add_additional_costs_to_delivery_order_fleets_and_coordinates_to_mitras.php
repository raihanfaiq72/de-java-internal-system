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
            $table->json('additional_costs')->nullable()->after('estimated_fuel_cost');
        });

        Schema::table('mitras', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('alamat');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_order_fleets', function (Blueprint $table) {
            $table->dropColumn('additional_costs');
        });

        Schema::table('mitras', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
