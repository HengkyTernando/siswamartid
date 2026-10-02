<?php

namespace App\Services;

class BadWordFilterService
{
    protected CommentModerationService $moderationService;

    public function __construct(CommentModerationService $moderationService)
    {
        $this->moderationService = $moderationService;
    }

    /**
     * Filter dan sensor kata-kata tidak pantas dalam teks (Legacy Adapter).
     *
     * @param string|null $text
     * @return array{censored_text: string, is_censored: bool, found_words: array<string>}
     */
    public function filter(?string $text): array
    {
        $result = $this->moderationService->moderate($text);

        return [
            'censored_text' => $result['censored_text'],
            'is_censored'   => $result['detected'],
            'found_words'   => $result['words'],
        ];
    }
}
