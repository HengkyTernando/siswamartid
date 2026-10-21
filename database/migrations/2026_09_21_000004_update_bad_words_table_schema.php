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
        Schema::table('bad_words', function (Blueprint $table) {
            if (!Schema::hasColumn('bad_words', 'language')) {
                $table->string('language', 30)->default('indonesian')->after('word');
            }
            if (!Schema::hasColumn('bad_words', 'severity')) {
                $table->string('severity', 20)->default('medium')->after('language'); // low, medium, high
            }
            if (!Schema::hasColumn('bad_words', 'category')) {
                $table->string('category', 50)->nullable()->after('severity');
            }
            if (!Schema::hasColumn('bad_words', 'type')) {
                $table->string('type', 20)->default('custom')->after('category'); // builtin, custom
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bad_words', function (Blueprint $table) {
            $columns = [];
            foreach (['language', 'severity', 'category', 'type'] as $col) {
                if (Schema::hasColumn('bad_words', $col)) {
                    $columns[] = $col;
                }
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
