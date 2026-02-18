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
        Schema::create('salary_slips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salary_period_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');

            // Earnings
            $table->decimal('basic_salary', 15, 2)->default(0); // Gaji Pokok/Harian Total
            $table->decimal('premi', 15, 2)->default(0);
            $table->decimal('meal_allowance', 15, 2)->default(0); // Uang Makan Total

            // Deductions
            $table->decimal('late_deduction', 15, 2)->default(0);
            $table->decimal('other_deduction', 15, 2)->default(0); // Kasbon/Lainnya

            // Total
            $table->decimal('total_salary', 15, 2)->default(0);

            // Details (JSON for breakdown)
            $table->json('details')->nullable();
            // e.g. { "work_days": 20, "meal_days": 6, "late_count": 14, "late_threshold_penalty": 200000, "daily_rate": 50000, "meal_rate": 15000 }

            $table->enum('status', ['draft', 'published', 'paid'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_slips');
    }
};
