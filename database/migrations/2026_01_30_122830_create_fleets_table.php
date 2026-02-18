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
        Schema::create('fleets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained()->cascadeOnDelete();

            $table->string('fleet_name', 100);
            $table->string('fuel_type', 50);
            $table->string('license_plate', 20)->unique();

            // efficiency example: 12 km per liter
            $table->decimal('km_per_liter', 6, 2)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fleets');
    }
};
