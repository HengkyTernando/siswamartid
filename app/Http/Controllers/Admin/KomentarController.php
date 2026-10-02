<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BadWord;
use App\Models\CommentModerationLog;
use App\Models\Ulasan;
use App\Models\Whitelist;
use App\Services\CommentModerationService;
use App\Services\AdminNotificationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class KomentarController extends Controller
{
    /**
     * Tampilkan halaman Moderasi Komentar & Filter Kata.
     */
    public function index(Request $request): View
    {
        $search = $request->query('q');
        $status = $request->query('status', 'filtered'); // Default ke 'filtered' (Perlu Moderasi)
        $rating = $request->query('rating');
        $sort   = $request->query('sort', 'terbaru');
        $tab    = $request->query('tab', 'komentar');

        // ── Data Real Statistik Moderasi ──────────────────────────────────────
        $stats = [
            'total'          => Ulasan::count(),
            'perlu_moderasi' => Ulasan::where('moderation_status', 'filtered')
                ->orWhere(fn($q) => $q->whereNull('moderation_status')->where('status', 'filtered'))
                ->count(),
            'approved'       => Ulasan::where('moderation_status', 'approved')
                ->orWhere(fn($q) => $q->whereNull('moderation_status')->whereIn('status', ['aktif', 'disetujui', 'disensor']))
                ->count(),
            'rejected'       => Ulasan::where('moderation_status', 'rejected')
                ->orWhere(fn($q) => $q->whereNull('moderation_status')->whereIn('status', ['disembunyikan', 'ditolak', 'dihapus']))
                ->count(),
        ];

        // ── Query List Komentar ───────────────────────────────────────────────
        $query = Ulasan::with(['produk', 'moderator', 'moderationLogs.admin']);

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('komentar', 'like', "%{$search}%")
                  ->orWhere('original_komentar', 'like', "%{$search}%")
                  ->orWhere('guest_username', 'like', "%{$search}%")
                  ->orWhereHas('produk', function ($pq) use ($search) {
                      $pq->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        // Filter Status
        if ($status && $status !== 'semua') {
            if ($status === 'filtered' || $status === 'perlu-moderasi') {
                $query->where(function ($q) {
                    $q->where('moderation_status', 'filtered')
                      ->orWhere(fn($sub) => $sub->whereNull('moderation_status')->where('status', 'filtered'));
                });
            } elseif ($status === 'approved' || $status === 'tampil') {
                $query->where(function ($q) {
                    $q->where('moderation_status', 'approved')
                      ->orWhere(fn($sub) => $sub->whereNull('moderation_status')->whereIn('status', ['aktif', 'disetujui', 'disensor']));
                });
            } elseif ($status === 'rejected' || $status === 'ditolak') {
                $query->where(function ($q) {
                    $q->where('moderation_status', 'rejected')
                      ->orWhere(fn($sub) => $sub->whereNull('moderation_status')->whereIn('status', ['disembunyikan', 'ditolak', 'dihapus']));
                });
            } else {
                $query->where('moderation_status', $status);
            }
        }

        // Filter Rating
        if ($rating && $rating !== 'semua') {
            $query->where('rating', (int) $rating);
        }

        // Sorting
        if ($sort === 'terlama') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $komentarList = $query->paginate(10)->withQueryString();

        // ── Data Badwords & Whitelists ────────────────────────────────────────
        $badWords   = BadWord::orderBy('word', 'asc')->get();
        $whitelists = Whitelist::orderBy('phrase', 'asc')->get();

        return view('admin.komentar.index', compact(
            'stats',
            'komentarList',
            'badWords',
            'whitelists',
            'search',
            'status',
            'rating',
            'sort',
            'tab'
        ));
    }

    /**
     * Aksi Admin A: TAMPILKAN (Approve).
     * Memublikasikan komentar asli tanpa mengubah teks asli.
     */
    public function approve(Ulasan $ulasan): RedirectResponse
    {
        $originalStatus = $ulasan->moderation_status ?? $ulasan->status;

        $ulasan->update([
            'moderation_status' => 'approved',
            'status'            => 'approved',
            'moderated_at'      => now(),
            'moderated_by'      => auth()->id(),
        ]);

        CommentModerationLog::create([
            'ulasan_id'       => $ulasan->id,
            'admin_id'        => auth()->id(),
            'action'          => 'approved',
            'original_status' => $originalStatus,
            'new_status'      => 'approved',
        ]);

        return back()->with('success', 'Komentar disetujui dan ditampilkan.');
    }

    /**
     * Aksi Admin B: SENSOR (Censor).
     * Menyensor kata sensitif ke display_content, tetapi komentar asli TETAP UTUH.
     */
    public function censor(Request $request, Ulasan $ulasan, CommentModerationService $moderationService, AdminNotificationService $notificationService): RedirectResponse
    {
        $originalStatus = $ulasan->moderation_status ?? $ulasan->status;

        // Gunakan display_content dari request atau hasil sensor otomatis dari service
        $displayContent = $request->input('display_content');
        if (empty($displayContent)) {
            $modResult = $moderationService->moderate($ulasan->komentar);
            $displayContent = $modResult['censored_text'];
        }

        $ulasan->update([
            'display_content'   => $displayContent,
            'is_censored'       => true,
            'moderation_status' => 'approved',
            'status'            => 'approved',
            'moderated_at'      => now(),
            'moderated_by'      => auth()->id(),
        ]);

        CommentModerationLog::create([
            'ulasan_id'       => $ulasan->id,
            'admin_id'        => auth()->id(),
            'action'          => 'censored',
            'original_status' => $originalStatus,
            'new_status'      => 'approved',
        ]);

        $notificationService->notifyAdmins([
            'title' => 'Komentar disensor',
            'description' => 'Komentar ' . ($ulasan->guest_username ?? 'pengguna') . ' pada produk "' . ($ulasan->produk?->nama ?? 'produk') . '" disensor oleh ' . auth()->user()->name . '.',
            'activity' => 'ulasan.disensor',
            'actor' => auth()->user()->name,
            'entity_type' => 'ulasan',
            'entity_id' => $ulasan->id,
            'url' => route('admin.komentar.index', ['status' => 'semua', 'tab' => 'komentar']),
        ], auth()->id());

        return back()->with('success', 'Komentar disensor dan ditampilkan.');
    }

    /**
     * Aksi Admin C: TOLAK (Reject).
     * Komentar tetap ada di database, namun tidak ditampilkan ke publik.
     */
    public function reject(Request $request, Ulasan $ulasan): RedirectResponse
    {
        $originalStatus = $ulasan->moderation_status ?? $ulasan->status;

        $ulasan->update([
            'moderation_status' => 'rejected',
            'status'            => 'rejected',
            'moderation_reason' => $request->input('reason'),
            'moderated_at'      => now(),
            'moderated_by'      => auth()->id(),
        ]);

        CommentModerationLog::create([
            'ulasan_id'       => $ulasan->id,
            'admin_id'        => auth()->id(),
            'action'          => 'rejected',
            'original_status' => $originalStatus,
            'new_status'      => 'rejected',
        ]);

        return back()->with('success', 'Komentar ditolak.');
    }

    /**
     * Legacy Hide handler.
     */
    public function hide(Ulasan $ulasan): RedirectResponse
    {
        return $this->reject(request(), $ulasan);
    }

    /**
     * Hapus Komentar dari database.
     */
    public function destroy(Ulasan $ulasan, AdminNotificationService $notificationService): RedirectResponse
    {
        $description = 'Komentar ' . ($ulasan->guest_username ?? 'pengguna') . ' pada produk "' . ($ulasan->produk?->nama ?? 'produk') . '" dihapus oleh ' . auth()->user()->name . '.';
        $ulasanId = $ulasan->id;
        $ulasan->delete();

        $notificationService->notifyAdmins([
            'title' => 'Komentar dihapus',
            'description' => $description,
            'activity' => 'ulasan.dihapus',
            'actor' => auth()->user()->name,
            'entity_type' => 'ulasan',
            'entity_id' => $ulasanId,
            'url' => route('admin.komentar.index', ['status' => 'semua', 'tab' => 'komentar']),
        ], auth()->id());
        return back()->with('success', 'Komentar dihapus.');
    }

    // ─── Management Bad Words ─────────────────────────────────────────────────

    public function storeBadWord(Request $request, CommentModerationService $service): RedirectResponse
    {
        $validated = $request->validate([
            'word'     => ['required', 'string', 'max:50', 'unique:bad_words,word'],
            'language' => ['nullable', 'string', 'in:indonesian,english'],
            'severity' => ['nullable', 'string', 'in:low,medium,high'],
            'category' => ['nullable', 'string', 'max:50'],
        ]);

        BadWord::create([
            'word'      => strtolower(trim($validated['word'])),
            'language'  => $validated['language'] ?? 'indonesian',
            'severity'  => $validated['severity'] ?? 'medium',
            'category'  => $validated['category'] ?? null,
            'type'      => 'custom',
            'is_active' => true,
        ]);

        $service->clearCache();

        return redirect()->route('admin.komentar.index', ['tab' => 'filter-kata'])
            ->with('success', 'Kata filter berhasil ditambahkan.');
    }

    public function updateBadWord(Request $request, BadWord $badWord, CommentModerationService $service): RedirectResponse
    {
        $validated = $request->validate([
            'word'     => ['required', 'string', 'max:50', 'unique:bad_words,word,' . $badWord->id],
            'language' => ['nullable', 'string', 'in:indonesian,english'],
            'severity' => ['nullable', 'string', 'in:low,medium,high'],
            'category' => ['nullable', 'string', 'max:50'],
        ]);

        $badWord->update([
            'word'     => strtolower(trim($validated['word'])),
            'language' => $validated['language'] ?? $badWord->language,
            'severity' => $validated['severity'] ?? $badWord->severity,
            'category' => $validated['category'] ?? $badWord->category,
        ]);

        $service->clearCache();

        return redirect()->route('admin.komentar.index', ['tab' => 'filter-kata'])
            ->with('success', 'Kata filter berhasil diperbarui.');
    }

    public function toggleBadWord(BadWord $badWord, CommentModerationService $service): RedirectResponse
    {
        $badWord->update([
            'is_active' => !$badWord->is_active,
        ]);

        $service->clearCache();

        $statusMsg = $badWord->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.komentar.index', ['tab' => 'filter-kata'])
            ->with('success', "Kata filter '{$badWord->word}' berhasil {$statusMsg}.");
    }

    public function destroyBadWord(BadWord $badWord, CommentModerationService $service): RedirectResponse
    {
        $badWord->delete();
        $service->clearCache();

        return redirect()->route('admin.komentar.index', ['tab' => 'filter-kata'])
            ->with('success', 'Kata filter berhasil dihapus.');
    }

    // ─── Management Whitelist ─────────────────────────────────────────────────

    public function storeWhitelist(Request $request, CommentModerationService $service): RedirectResponse
    {
        $validated = $request->validate([
            'phrase' => ['required', 'string', 'max:100', 'unique:whitelists,phrase'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        Whitelist::create([
            'phrase'    => strtolower(trim($validated['phrase'])),
            'reason'    => $validated['reason'] ?? null,
            'is_active' => true,
        ]);

        $service->clearCache();

        return redirect()->route('admin.komentar.index', ['tab' => 'whitelist'])
            ->with('success', 'Pengecualian whitelist berhasil ditambahkan.');
    }

    public function toggleWhitelist(Whitelist $whitelist, CommentModerationService $service): RedirectResponse
    {
        $whitelist->update([
            'is_active' => !$whitelist->is_active,
        ]);

        $service->clearCache();

        $statusMsg = $whitelist->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.komentar.index', ['tab' => 'whitelist'])
            ->with('success', "Whitelist '{$whitelist->phrase}' berhasil {$statusMsg}.");
    }

    public function destroyWhitelist(Whitelist $whitelist, CommentModerationService $service): RedirectResponse
    {
        $whitelist->delete();
        $service->clearCache();

        return redirect()->route('admin.komentar.index', ['tab' => 'whitelist'])
            ->with('success', 'Whitelist berhasil dihapus.');
    }
}
