<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Ulasan;
use App\Services\CommentModerationService;
use App\Services\AdminNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class UlasanController extends Controller
{
    /**
     * Simpan ulasan guest baru dengan auto-moderation filter.
     */
    public function store(Request $request, Produk $produk, CommentModerationService $moderationService, AdminNotificationService $notificationService): RedirectResponse
    {
        abort_if($produk->status !== 'aktif', 404);

        $validated = $request->validate([
            'guest_username' => ['required', 'string', 'max:100'],
            'rating'         => ['required', 'integer', 'min:1', 'max:5'],
            'komentar'       => ['nullable', 'string', 'max:1000'],
        ]);

        $originalText = $validated['komentar'] ?? '';

        // Periksa badword dan whitelist melalui CommentModerationService
        $modResult = $moderationService->moderate($originalText);

        $isFiltered = $modResult['detected'];
        $status = $isFiltered ? 'filtered' : 'approved';

        $ulasan = Ulasan::create([
            'produk_id'         => $produk->id,
            'guest_username'    => $validated['guest_username'],
            'rating'            => $validated['rating'],
            'komentar'          => $originalText,
            'original_komentar' => $originalText,
            'display_content'   => null,
            'is_censored'       => false,
            'moderation_status' => $status,
            'status'            => $status,
            'detected_words'    => $isFiltered ? [
                'words'    => $modResult['words'],
                'severity' => $modResult['severity'],
                'count'    => $modResult['match_count'],
            ] : null,
        ]);

        $notificationService->notifyAdmins([
            'title' => 'Ulasan baru masuk',
            'description' => $validated['guest_username'] . ' mengirim ulasan untuk produk "' . $produk->nama . '".',
            'activity' => 'ulasan.ditambahkan',
            'actor' => $validated['guest_username'],
            'entity_type' => 'ulasan',
            'entity_id' => $ulasan->id,
            'url' => route('admin.komentar.index', ['status' => 'semua', 'tab' => 'komentar']),
        ]);

        $msg = $isFiltered
            ? 'Ulasanmu sudah dikirim dan sedang dicek Admin.'
            : 'Ulasanmu berhasil dikirim.';

        return redirect()->route('public.produk.show', $produk->slug)
            ->with('success', $msg);
    }
}
