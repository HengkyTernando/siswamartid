<?php

namespace Database\Seeders;

use App\Models\BadWord;
use App\Services\CommentModerationService;
use Illuminate\Database\Seeder;

class BadWordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultBadWords = [
            // Indonesian
            ['word' => 'anjing',   'language' => 'indonesian', 'severity' => 'medium', 'category' => 'hewan/makian'],
            ['word' => 'asu',      'language' => 'indonesian', 'severity' => 'medium', 'category' => 'hewan/makian'],
            ['word' => 'babi',     'language' => 'indonesian', 'severity' => 'medium', 'category' => 'hewan/makian'],
            ['word' => 'bajingan', 'language' => 'indonesian', 'severity' => 'high',   'category' => 'hinaan'],
            ['word' => 'bangsat',  'language' => 'indonesian', 'severity' => 'high',   'category' => 'hinaan'],
            ['word' => 'kontol',   'language' => 'indonesian', 'severity' => 'high',   'category' => 'porno/vulkan'],
            ['word' => 'memek',    'language' => 'indonesian', 'severity' => 'high',   'category' => 'porno/vulkan'],
            ['word' => 'goblok',   'language' => 'indonesian', 'severity' => 'low',    'category' => 'hinaan'],
            ['word' => 'tolol',    'language' => 'indonesian', 'severity' => 'low',    'category' => 'hinaan'],
            ['word' => 'kampret',  'language' => 'indonesian', 'severity' => 'medium', 'category' => 'makian'],
            ['word' => 'pantek',   'language' => 'indonesian', 'severity' => 'high',   'category' => 'makian'],
            ['word' => 'itil',     'language' => 'indonesian', 'severity' => 'high',   'category' => 'porno/vulkan'],
            ['word' => 'jembud',   'language' => 'indonesian', 'severity' => 'high',   'category' => 'porno/vulkan'],
            ['word' => 'perek',    'language' => 'indonesian', 'severity' => 'high',   'category' => 'hinaan'],
            
            // English
            ['word' => 'fuck',     'language' => 'english',    'severity' => 'high',   'category' => 'profanity'],
            ['word' => 'shit',     'language' => 'english',    'severity' => 'medium', 'category' => 'profanity'],
            ['word' => 'bitch',    'language' => 'english',    'severity' => 'high',   'category' => 'profanity'],
            ['word' => 'bastard',  'language' => 'english',    'severity' => 'high',   'category' => 'profanity'],
            ['word' => 'asshole',  'language' => 'english',    'severity' => 'medium', 'category' => 'profanity'],
        ];

        foreach ($defaultBadWords as $data) {
            BadWord::updateOrCreate(
                ['word' => strtolower(trim($data['word']))],
                [
                    'language'  => $data['language'],
                    'severity'  => $data['severity'],
                    'category'  => $data['category'],
                    'type'      => 'builtin',
                    'is_active' => true,
                ]
            );
        }

        app(CommentModerationService::class)->clearCache();
    }
}
