<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         // Skip this migration to avoid unique constraint violation
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optional: delete added accounts
    }
};
