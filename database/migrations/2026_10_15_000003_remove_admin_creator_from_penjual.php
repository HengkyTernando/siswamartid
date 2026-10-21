<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('penjual', 'created_by_admin_id')) {
            return;
        }

        Schema::table('penjual', function (Blueprint $table) {
            $table->dropForeign(['created_by_admin_id']);
            $table->dropColumn('created_by_admin_id');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('penjual', 'created_by_admin_id')) {
            return;
        }

        Schema::table('penjual', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by_admin_id')->nullable()->after('user_id');
            $table->foreign('created_by_admin_id')
                ->references('id')->on('users')
                ->nullOnDelete();
        });
    }
};
