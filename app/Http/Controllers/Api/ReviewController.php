<?php

namespace App\Http\Controllers\Api;

use App\Models\Produk;
use App\Models\Ulasan;
use App\Services\CommentModerationService;
use Illuminate\Http\Request;

class ReviewController extends ApiController
{
    /**
     * Menampilkan daftar ulasan publik untuk sebuah produk.
     */
    public function index(Produk $product)
    {
        abort_if($product->status !== 'aktif', 404);

        $reviews = $product->ulasan()
            ->publicVisible()
            ->latest()
            ->get()
            ->map(fn (Ulasan $review) => $this->reviewData($review));

        return $this->success(
            $reviews,
            'Daftar ulasan berhasil diambil'
        );
    }

    /**
     * Menambahkan ulasan.
     *
     * Endpoint ini dapat digunakan oleh guest.
     */
    public function store(
        Request $request,
        Produk $product,
        CommentModerationService $moderation
    ) {
        abort_if($product->status !== 'aktif', 404);

        $validated = $request->validate([
            'guest_username' => ['required', 'string', 'max:100'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'komentar' => ['nullable', 'string', 'max:1000'],
        ]);

        /*
         * Ambil komentar dari field "comment".
         * Tetap mendukung "komentar" untuk kompatibilitas dengan
         * request lama.
         */
        $comment = $validated['comment']
            ?? $validated['komentar']
            ?? '';

        /*
         * Bersihkan username dan komentar.
         */
        $guestUsername = trim($validated['guest_username']);
        $comment = trim($comment);

        /*
         * Moderasi komentar.
         */
        $moderated = $moderation->moderate($comment);

        $status = $moderated['detected']
            ? 'filtered'
            : 'approved';

        /*
         * Simpan ulasan.
         */
        $review = $product->ulasan()->create([
            'guest_username' => $guestUsername,
            'rating' => $validated['rating'],
            'komentar' => $comment,
            'original_komentar' => $comment,
            'display_content' => null,
            'is_censored' => false,
            'moderation_status' => $status,
            'status' => $status,

            'detected_words' => $moderated['detected']
                ? [
                    'words' => $moderated['words'],
                    'severity' => $moderated['severity'],
                    'count' => $moderated['match_count'],
                ]
                : null,
        ]);

        $message = $moderated['detected']
            ? 'Ulasan dikirim dan menunggu moderasi admin.'
            : 'Ulasan berhasil dikirim.';

        return $this->success(
            $this->reviewData($review),
            $message,
            201
        );
    }

    /**
     * Format data ulasan untuk API.
     */
    private function reviewData(Ulasan $review): array
    {
        return [
            'id' => $review->id,
            'name' => $review->guest_username,
            'rating' => $review->rating,
            'comment' => $review->public_content,
            'date' => $review->created_at?->toDateString(),
        ];
    }
}