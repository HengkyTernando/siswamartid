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
        Schema::table('ulasan', function (Blueprint $table) {
            if (!Schema::hasColumn('ulasan', 'moderation_status')) {
                $table->string('moderation_status', 30)->default('approved')->after('status');
            }
            if (!Schema::hasColumn('ulasan', 'display_content')) {
                $table->text('display_content')->nullable()->after('original_komentar');
            }
            if (!Schema::hasColumn('ulasan', 'detected_words')) {
                $table->json('detected_words')->nullable()->after('display_content');
            }
            if (!Schema::hasColumn('ulasan', 'moderation_reason')) {
                $table->text('moderation_reason')->nullable()->after('detected_words');
            }
            if (!Schema::hasColumn('ulasan', 'moderated_at')) {
                $table->timestamp('moderated_at')->nullable()->after('moderation_reason');
            }
            if (!Schema::hasColumn('ulasan', 'moderated_by')) {
                $table->foreignId('moderated_by')->nullable()->after('moderated_at')->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ulasan', function (Blueprint $table) {
            if (Schema::hasColumn('ulasan', 'moderated_by')) {
                $table->dropForeign(['moderated_by']);
                $table->dropColumn('moderated_by');
            }
            $columnsToDrop = [];
            foreach (['moderation_status', 'display_content', 'detected_words', 'moderation_reason', 'moderated_at'] as $col) {
                if (Schema::hasColumn('ulasan', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
