<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username', 80)->nullable()->after('name');
            });
        }

        foreach (DB::table('users')->whereNull('username')->orderBy('id')->get(['id', 'name', 'email']) as $user) {
            $identity = Str::before($user->email ?: $user->name, '@');
            $base = Str::limit(Str::slug($identity, '_') ?: 'user', 70, '');
            $username = $base;
            $suffix = 1;

            while (DB::table('users')->where('username', $username)->exists()) {
                $tail = '_' . $suffix++;
                $username = Str::limit($base, 80 - strlen($tail), '') . $tail;
            }

            DB::table('users')->where('id', $user->id)->update(['username' => $username]);
        }

        if (DB::table('penjual')->select('user_id')->groupBy('user_id')->havingRaw('COUNT(*) > 1')->exists()) {
            throw new RuntimeException('Tidak dapat menambahkan indeks unik: ditemukan lebih dari satu profil untuk akun yang sama.');
        }

        if (DB::table('penjual')->whereNotNull('nisn')->select('nisn')->groupBy('nisn')->havingRaw('COUNT(*) > 1')->exists()) {
            throw new RuntimeException('Tidak dapat menambahkan indeks unik NISN: ditemukan nilai NISN duplikat.');
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 80)->nullable(false)->change();
            $table->unique('username', 'users_username_unique');
        });

        Schema::table('penjual', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by_admin_id')->nullable()->after('user_id');
            $table->unique('user_id', 'penjual_user_id_unique');
            $table->unique('nisn', 'penjual_nisn_unique');
            $table->foreign('created_by_admin_id')
                ->references('id')->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('penjual', function (Blueprint $table) {
            $table->dropForeign(['created_by_admin_id']);
            $table->dropUnique('penjual_user_id_unique');
            $table->dropUnique('penjual_nisn_unique');
            $table->dropColumn('created_by_admin_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_username_unique');
            $table->dropColumn('username');
        });
    }
};
