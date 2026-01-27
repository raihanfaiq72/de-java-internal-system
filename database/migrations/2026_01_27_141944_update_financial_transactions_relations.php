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
        Schema::table('financial_transactions', function (Blueprint $table) {
            // Drop foreign keys to chart_of_accounts
            // Note: The constraint name might vary, usually table_column_foreign
            $table->dropForeign(['from_account_id']);
            $table->dropForeign(['to_account_id']);
            
            // We keep the columns, but now they can store FinancialAccount IDs OR COA IDs
            // Ideally we would rename them or add type columns, but for now just removing FK constraint allows flexibility
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financial_transactions', function (Blueprint $table) {
            // Restore FKs if needed (might fail if data is mixed)
        });
    }
};
