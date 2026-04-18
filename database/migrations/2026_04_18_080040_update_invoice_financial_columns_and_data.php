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
        Schema::table('invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('invoices', 'cashback')) {
                $table->decimal('cashback', 15, 2)->default(0);
            }
            if (! Schema::hasColumn('invoices', 'other_fee')) {
                $table->decimal('other_fee', 15, 2)->default(0);
            }
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            if (! Schema::hasColumn('invoice_items', 'diskon_tipe')) {
                $table->string('diskon_tipe')->default('Nominal');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('diskon_tipe');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['cashback', 'other_fee']);
        });
    }
};
