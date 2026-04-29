<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add Return to tipe_invoice enum
        DB::statement("ALTER TABLE invoices MODIFY COLUMN tipe_invoice ENUM('Sales', 'Purchase', 'SuratJalan', 'Return') NOT NULL");

        // Add reference to original invoice
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('return_of_invoice_id')->nullable()->after('ref_no');
            $table->foreign('return_of_invoice_id')->references('id')->on('invoices')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['return_of_invoice_id']);
            $table->dropColumn('return_of_invoice_id');
        });

        DB::statement("DELETE FROM invoices WHERE tipe_invoice = 'Return'");
        DB::statement("ALTER TABLE invoices MODIFY COLUMN tipe_invoice ENUM('Sales', 'Purchase', 'SuratJalan') NOT NULL");
    }
};
