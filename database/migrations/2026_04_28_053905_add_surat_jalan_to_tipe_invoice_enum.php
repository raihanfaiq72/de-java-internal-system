<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: alter the enum to add SuratJalan
        DB::statement("ALTER TABLE invoices MODIFY COLUMN tipe_invoice ENUM('Sales', 'Purchase', 'SuratJalan') NOT NULL");
    }

    public function down(): void
    {
        // Remove SuratJalan entries first to avoid data truncation errors
        DB::statement("DELETE FROM invoices WHERE tipe_invoice = 'SuratJalan'");
        DB::statement("ALTER TABLE invoices MODIFY COLUMN tipe_invoice ENUM('Sales', 'Purchase') NOT NULL");
    }
};
