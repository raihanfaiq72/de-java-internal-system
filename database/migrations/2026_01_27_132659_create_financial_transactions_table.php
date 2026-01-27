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
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained('offices')->onDelete('cascade');
            $table->date('transaction_date');
            $table->enum('type', ['transfer', 'income', 'expense']);
            
            // For transfer: from = source, to = destination
            // For income: to = destination account (source might be null or specific COA)
            // For expense: from = source account (destination might be null or specific COA)
            $table->foreignId('from_account_id')->nullable()->constrained('chart_of_accounts');
            $table->foreignId('to_account_id')->nullable()->constrained('chart_of_accounts');
            
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('lampiran')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
