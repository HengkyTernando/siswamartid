<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjual', function (Blueprint $table) {
            if (! Schema::hasColumn('penjual', 'nisn')) {
                $table->string('nisn', 20)->nullable()->unique()->after('user_id');
            }
            if (! Schema::hasColumn('penjual', 'alamat')) {
                $table->text('alamat')->nullable()->after('lokasi_kelas');
            }
            if (! Schema::hasColumn('penjual', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('updated_at');
            }
            if (! Schema::hasColumn('penjual', 'last_activity_at')) {
                $table->timestamp('last_activity_at')->nullable()->after('last_login_at');
            }
            if (! Schema::hasColumn('penjual', 'last_activity_type')) {
                $table->string('last_activity_type', 80)->nullable()->after('last_activity_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('penjual', function (Blueprint $table) {
            $table->dropUnique(['nisn']);
            $table->dropColumn([
                'nisn',
                'alamat',
                'last_login_at',
                'last_activity_at',
                'last_activity_type',
            ]);
        });
    }
};