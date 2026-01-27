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
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('office_id')->constrained('chart_of_accounts')->nullOnDelete();
            $table->string('tipe_akun')->nullable()->after('is_kas_bank'); // Cash, Bank, Corporate Card
            $table->string('bank_name')->nullable()->after('tipe_akun');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('bank_account_name')->nullable()->after('bank_account_number');
            $table->string('bank_branch')->nullable()->after('bank_account_name');
            $table->string('bank_city')->nullable()->after('bank_branch');
            $table->string('currency')->default('IDR')->after('bank_city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn([
                'parent_id',
                'tipe_akun',
                'bank_name',
                'bank_account_number',
                'bank_account_name',
                'bank_branch',
                'bank_city',
                'currency'
            ]);
        });
    }
};
