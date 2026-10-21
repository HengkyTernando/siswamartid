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
            if (!Schema::hasColumn('ulasan', 'original_komentar')) {
                $table->text('original_komentar')->nullable()->after('komentar');
            }
            if (!Schema::hasColumn('ulasan', 'is_censored')) {
                $table->boolean('is_censored')->default(false)->after('original_komentar');
            }
        });

        // Ubah tipe status menjadi string untuk mendukung 'aktif', 'disetujui', 'disensor', 'disembunyikan', 'ditolak', 'dihapus'
        try {
            Schema::table('ulasan', function (Blueprint $table) {
                $table->string('status', 30)->default('aktif')->change();
            });
        } catch (\Throwable $e) {
            // Jika driver tidak mendukung direct change (misal SQLite tanpa doctrine), abaikan atau tangani secara opsional
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ulasan', function (Blueprint $table) {
            if (Schema::hasColumn('ulasan', 'original_komentar')) {
                $table->dropColumn('original_komentar');
            }
            if (Schema::hasColumn('ulasan', 'is_censored')) {
                $table->dropColumn('is_censored');
            }
        });
    }
};
