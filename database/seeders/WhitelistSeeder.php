<?php

namespace Database\Seeders;

use App\Models\Whitelist;
use App\Services\CommentModerationService;
use Illuminate\Database\Seeder;

class WhitelistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultWhitelists = [
            ['phrase' => 'mie ayam babi', 'reason' => 'Nama varian produk/kuliner khusus'],
            ['phrase' => 'babi guling',   'reason' => 'Nama masakan tradisional'],
        ];

        foreach ($defaultWhitelists as $data) {
            Whitelist::firstOrCreate(
                ['phrase' => strtolower(trim($data['phrase']))],
                [
                    'reason'    => $data['reason'],
                    'is_active' => true,
                ]
            );
        }

        app(CommentModerationService::class)->clearCache();
    }
}
