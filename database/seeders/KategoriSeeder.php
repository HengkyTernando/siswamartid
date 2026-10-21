<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Membuat kategori awal SiswaMart.
     * Menggunakan updateOrCreate agar aman dijalankan ulang.
     */
    public function run(): void
    {
        $kategori = [
            ['nama' => 'Makanan',  'slug' => 'makanan',  'icon' => '🍱'],
            ['nama' => 'Minuman',  'slug' => 'minuman',  'icon' => '🥤'],
            ['nama' => 'Snack',    'slug' => 'snack',    'icon' => '🍿'],
            ['nama' => 'Dessert',  'slug' => 'dessert',  'icon' => '🍰'],
        ];

        foreach ($kategori as $item) {
            Kategori::updateOrCreate(
                // Kunci pencarian — berdasarkan slug
                ['slug' => $item['slug']],
                // Data yang dibuat atau diperbarui
                [
                    'nama' => $item['nama'],
                    'icon' => $item['icon'],
                ]
            );
        }

        $this->command->info('✅ ' . count($kategori) . ' kategori berhasil dibuat.');
    }
}
