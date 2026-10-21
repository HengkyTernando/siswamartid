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
        if (!Schema::hasTable('comment_moderation_logs')) {
            Schema::create('comment_moderation_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ulasan_id')->constrained('ulasan')->onDelete('cascade');
                $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action', 50); // approved, censored, rejected
                $table->string('original_status', 30)->nullable();
                $table->string('new_status', 30);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_moderation_logs');
    }
};
