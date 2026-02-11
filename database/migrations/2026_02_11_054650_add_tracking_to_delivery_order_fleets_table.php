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
            $table->decimal('start_latitude', 10, 8)->nullable()->after('additional_costs');
            $table->decimal('start_longitude', 11, 8)->nullable()->after('start_latitude');
            $table->decimal('last_latitude', 10, 8)->nullable()->after('start_longitude');
            $table->decimal('last_longitude', 11, 8)->nullable()->after('last_latitude');
            $table->enum('status', ['assigned', 'in_transit', 'completed'])->default('assigned')->after('driver_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_order_fleets', function (Blueprint $table) {
            $table->dropColumn(['start_latitude', 'start_longitude', 'last_latitude', 'last_longitude', 'status']);
        });
    }
};
