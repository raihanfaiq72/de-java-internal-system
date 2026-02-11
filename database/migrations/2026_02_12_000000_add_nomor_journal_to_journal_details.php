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
        if (!Schema::hasColumn('journal_details', 'nomor_journal')) {
            Schema::table('journal_details', function (Blueprint $table) {
                $table->string('nomor_journal')->after('id')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('journal_details', 'nomor_journal')) {
            Schema::table('journal_details', function (Blueprint $table) {
                $table->dropColumn('nomor_journal');
            });
        }
    }
};
