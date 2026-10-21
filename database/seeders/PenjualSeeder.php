<?php

namespace Database\Seeders;

use App\Models\Penjual;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PenjualSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Membuat akun Penjual dummy untuk SiswaMart.
     * Menggunakan updateOrCreate agar aman dijalankan ulang.
     */
    public function run(): void
    {
        $penjual = [
            [
                'user' => [
                    'name'      => 'Rina Kelas 11A',
                    'email'     => 'rina@siswamart.test',
                    'password'  => Hash::make(env('SEED_PENJUAL_PASSWORD', config('app.env') === 'local' ? 'password123' : \Illuminate\Support\Str::random(32))),
                    'role'      => 'penjual',
                    'is_active' => true,
                ],
                'toko' => [
                    'nama_toko'       => 'Dapur Rina',
                    'deskripsi_toko'  => 'Jajanan homemade enak dan murah dari dapur Rina.',
                    'nomor_whatsapp'  => '08111111001',
                    'lokasi_kelas'    => 'Kelas 11A',
                ],
            ],
            [
                'user' => [
                    'name'      => 'Budi Kelas 10B',
                    'email'     => 'budi@siswamart.test',
                    'password'  => Hash::make(env('SEED_PENJUAL_PASSWORD', config('app.env') === 'local' ? 'password123' : \Illuminate\Support\Str::random(32))),
                    'role'      => 'penjual',
                    'is_active' => true,
                ],
                'toko' => [
                    'nama_toko'       => 'Snack Budi',
                    'deskripsi_toko'  => 'Aneka snack kering dan minuman segar.',
                    'nomor_whatsapp'  => '08111111002',
                    'lokasi_kelas'    => 'Kelas 10B',
                ],
            ],
            [
                'user' => [
                    'name'      => 'Siti Kelas 12C',
                    'email'     => 'siti@siswamart.test',
                    'password'  => Hash::make(env('SEED_PENJUAL_PASSWORD', config('app.env') === 'local' ? 'password123' : \Illuminate\Support\Str::random(32))),
                    'role'      => 'penjual',
                    'is_active' => true,
                ],
                'toko' => [
                    'nama_toko'       => 'Sweet Siti',
                    'deskripsi_toko'  => 'Dessert dan minuman manis ala kafe.',
                    'nomor_whatsapp'  => '08111111003',
                    'lokasi_kelas'    => 'Kelas 12C',
                ],
            ],
        ];

        foreach ($penjual as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['user']['email']],
                $data['user']
            );

            Penjual::updateOrCreate(
                ['user_id' => $user->id],
                array_merge($data['toko'], ['user_id' => $user->id])
            );
        }

        $this->command->info('✅ ' . count($penjual) . ' penjual berhasil dibuat.');
    }
}
