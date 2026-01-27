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
        Schema::table('payments', function (Blueprint $table) {
            // Drop existing foreign key constraint
            // Laravel default naming: table_column_foreign
            $table->dropForeign(['akun_keuangan_id']);
            
            // We might need to make sure the column type matches, usually it is unsignedBigInteger
            // Since we are changing the referenced table, we just update the constraint
            $table->foreign('akun_keuangan_id')
                  ->references('id')
                  ->on('financial_accounts')
                  ->onDelete('restrict'); // Or cascade, but usually restrict for financial data
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['akun_keuangan_id']);
            
            $table->foreign('akun_keuangan_id')
                  ->references('id')
                  ->on('financial_accounts')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['akun_keuangan_id']);
            
            $table->foreign('akun_keuangan_id')
                  ->references('id')
                  ->on('chart_of_accounts');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['akun_keuangan_id']);
            
            $table->foreign('akun_keuangan_id')
                  ->references('id')
                  ->on('chart_of_accounts');
        });
    }
};
