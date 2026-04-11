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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('harga_tempo', 15, 2)->default(0)->after('harga_jual');
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->integer('tempo')->default(0)->after('qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('harga_tempo');
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('tempo');
        });
    }
};
