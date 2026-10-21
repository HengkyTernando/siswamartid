<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('produk', 'alasan_ditolak')) {
            Schema::table('produk', function (Blueprint $table) {
                $table->text('alasan_ditolak')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->dropColumn('alasan_ditolak');
        });
    }
};