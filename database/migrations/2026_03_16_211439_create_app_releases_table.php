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
        Schema::create('app_releases', function (Blueprint $table) {
            $table->id();
            $table->string('version');
            $table->string('build_number')->nullable();
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->bigInteger('file_size');
            $table->string('file_hash')->nullable(); // SHA256 hash for integrity
            $table->enum('platform', ['android', 'ios'])->default('android');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('is_latest')->default(false);
            $table->boolean('is_force_update')->default(false);
            $table->timestamp('release_date')->nullable();
            $table->json('changelog')->nullable(); // JSON array of changes
            $table->integer('download_count')->default(0);
            $table->timestamps();
            
            $table->index(['platform', 'status']);
            $table->index(['is_latest']);
            $table->unique(['version', 'platform']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_releases');
    }
};
