<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->string('pending_action', 20)->nullable()->after('alasan_ditolak');
            $table->json('pending_changes')->nullable()->after('pending_action');
        });
    }

    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->dropColumn(['pending_action', 'pending_changes']);
        });
    }
};
