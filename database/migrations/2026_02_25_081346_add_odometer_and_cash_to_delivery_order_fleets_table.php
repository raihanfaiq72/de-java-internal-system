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
        Schema::table('fleets', function (Blueprint $table) {
            if (!Schema::hasColumn('fleets', 'last_odometer')) {
                $table->decimal('last_odometer', 10, 2)->default(0)->after('km_per_liter');
            }
            if (!Schema::hasColumn('fleets', 'last_fuel_leftover')) {
                $table->decimal('last_fuel_leftover', 8, 2)->default(0)->after('last_odometer');
            }
        });

        Schema::table('delivery_order_fleets', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_order_fleets', 'odo_start')) {
                $table->decimal('odo_start', 10, 2)->default(0)->after('driver_id');
            }
            if (!Schema::hasColumn('delivery_order_fleets', 'odo_end')) {
                $table->decimal('odo_end', 10, 2)->default(0)->after('odo_start');
            }
            if (!Schema::hasColumn('delivery_order_fleets', 'cash_amount')) {
                $table->decimal('cash_amount', 15, 2)->default(0)->after('other_cost'); // Uang Jalan
            }
            if (!Schema::hasColumn('delivery_order_fleets', 'gas_leftover')) {
                $table->decimal('gas_leftover', 8, 2)->default(0)->after('cash_amount'); // Sisa Bensin
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fleets', function (Blueprint $table) {
            $table->dropColumn(['last_odometer', 'last_fuel_leftover']);
        });

        Schema::table('delivery_order_fleets', function (Blueprint $table) {
            $table->dropColumn(['odo_start', 'odo_end', 'cash_amount', 'gas_leftover']);
        });
    }
};
