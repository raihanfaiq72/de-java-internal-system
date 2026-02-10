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
            if (!Schema::hasColumn('delivery_order_invoices', 'chart_of_accounts_id')) {
                // Assuming invoice_id exists, we place it after invoice_id
                $table->foreignId('chart_of_accounts_id')
                    ->nullable() // Making it nullable initially to avoid issues with existing data, though we can update it later
                    ->after('invoice_id')
                    ->constrained('financial_accounts')
                    ->onDelete('restrict');
            }

            if (!Schema::hasColumn('delivery_order_invoices', 'total_cost')) {
                // If chart_of_accounts_id was just added or already exists, we place total_cost after it
                $afterColumn = Schema::hasColumn('delivery_order_invoices', 'chart_of_accounts_id') ? 'chart_of_accounts_id' : 'invoice_id';
                $table->decimal('total_cost', 15, 2)->default(0)->after($afterColumn);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_order_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_order_invoices', 'total_cost')) {
                $table->dropColumn('total_cost');
            }
            if (Schema::hasColumn('delivery_order_invoices', 'chart_of_accounts_id')) {
                $table->dropForeign(['chart_of_accounts_id']);
                $table->dropColumn('chart_of_accounts_id');
            }
        });
    }
};
