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
        Schema::table('delivery_order_invoices', function (Blueprint $table) {
            $table->renameColumn('delivery_queue', 'delivery_sequence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_order_invoices', function (Blueprint $table) {
            $table->renameColumn('delivery_sequence', 'delivery_queue');
        });
    }
};
