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
        if (! Schema::hasColumn('penjual', 'status_toko')) {
            Schema::table('penjual', function (Blueprint $table) {
                $table->string('status_toko', 20)->default('Buka')->after('foto_toko');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('penjual', 'status_toko')) {
            Schema::table('penjual', function (Blueprint $table) {
                $table->dropColumn('status_toko');
            });
        }
    }
};
