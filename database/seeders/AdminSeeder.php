<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Membuat akun Admin pertama SiswaMart.
     * Menggunakan updateOrCreate agar aman dijalankan ulang.
     */
    public function run(): void
    {
        User::updateOrCreate(
            // Kunci pencarian — berdasarkan email
            ['email' => 'admin@siswamart.test'],
            // Data yang dibuat atau diperbarui
            [
                'name'      => 'Admin SiswaMart',
                'password'  => Hash::make(env('SEED_ADMIN_PASSWORD', config('app.env') === 'local' ? 'password123' : \Illuminate\Support\Str::random(32))),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        $this->command->info('✅ Admin SiswaMart berhasil dibuat.');
    }
}
