<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Produk;
use App\Services\AdminNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProdukController extends Controller
{
    /**
     * Pastikan User login memiliki profil Penjual.
     */
    private function getPenjualId()
    {
        $penjual = auth()->user()->penjual;
        abort_unless($penjual, 403, 'Profil penjual tidak ditemukan.');
        return $penjual->id;
    }

    private function recordActivity(string $type): void
    {
        auth()->user()->penjual?->update([
            'last_activity_at' => now(),
            'last_activity_type' => $type,
        ]);
    }

    /**
     * Tampilkan daftar produk milik penjual.
     */
    public function index(): View
    {
        $penjualId = $this->getPenjualId();

        $produk = Produk::with('kategori')
            ->where('penjual_id', $penjualId)
            ->latest()
            ->paginate(15);

        return view('penjual.produk.index', compact('produk'));
    }

    /**
     * Tampilkan form tambah produk.
     */
    public function create(): View
    {
        // Pastikan punya profil penjual
        $this->getPenjualId();

        $kategori = Kategori::orderBy('nama')->get();

        return view('penjual.produk.create', compact('kategori'));
    }

    /**
     * Hasilkan slug unik.
     */
    private function generateUniqueSlug(string $nama, ?int $ignoreId = null): string
    {
        $slug = Str::slug($nama);
        $originalSlug = $slug;
        $count = 1;

        while (Produk::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Simpan produk baru.
     */
    public function store(Request $request, AdminNotificationService $notificationService): RedirectResponse
    {
        $penjualId = $this->getPenjualId();

        $validated = $request->validate([
            'nama'          => ['required', 'string', 'max:255'],
            'kategori_id'   => ['required', 'exists:kategori,id'],
            'deskripsi'     => ['required', 'string'],
            'harga'         => ['required', 'numeric', 'min:0'],
            'stok'          => ['required', 'in:tersedia,po,habis'],
            'label_promosi' => ['nullable', 'string', 'max:100'],
            'foto_utama'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $validated['penjual_id'] = $penjualId;
        $validated['slug']       = $this->generateUniqueSlug($request->nama);
        $validated['status']     = 'pending';

        // Konversi nilai 'tersedia' ke 'ready' dan 'habis' ke 'habis' sesuai enum db
        // Enum stok: 'ready', 'po', 'habis' -> user instruksinya tersedia/habis, 
        // kita mapping tersedia -> ready.
        $stokMapping = ['tersedia' => 'ready', 'po' => 'po', 'habis' => 'habis'];
        $validated['stok'] = $stokMapping[$request->stok];

        if ($request->hasFile('foto_utama')) {
            $validated['foto_utama'] = $request->file('foto_utama')->store('produk', 'public');
        }

        $produk = Produk::create($validated);
        $this->recordActivity('produk.ditambahkan');

        $notificationService->notifyAdmins([
            'title' => 'Produk baru ditambahkan',
            'description' => auth()->user()->name . ' menambahkan produk "' . $produk->nama . '".',
            'activity' => 'produk.ditambahkan',
            'actor' => auth()->user()->name,
            'entity_type' => 'produk',
            'entity_id' => $produk->id,
            'url' => route('admin.produk.show', $produk),
        ]);

        return redirect()->route('penjual.produk.index')
                         ->with('success', 'Produk berhasil dikirim dan menunggu pengecekan.');
    }

    /**
     * Tampilkan form edit produk.
     */
    public function edit(Produk $produk): View
    {
        $penjualId = $this->getPenjualId();
        abort_unless($produk->penjual_id === $penjualId, 403, 'Akses ditolak.');

        $kategori = Kategori::orderBy('nama')->get();

        return view('penjual.produk.edit', compact('produk', 'kategori'));
    }

    /**
     * Update produk.
     */
    public function update(Request $request, Produk $produk, AdminNotificationService $notificationService): RedirectResponse
    {
        $penjualId = $this->getPenjualId();
        abort_unless($produk->penjual_id === $penjualId, 403, 'Akses ditolak.');

        $validated = $request->validate([
            'nama'          => ['required', 'string', 'max:255'],
            'kategori_id'   => ['required', 'exists:kategori,id'],
            'deskripsi'     => ['required', 'string'],
            'harga'         => ['required', 'numeric', 'min:0'],
            'stok'          => ['required', 'in:tersedia,po,habis'],
            'label_promosi' => ['nullable', 'string', 'max:100'],
            'foto_utama'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->nama !== $produk->nama) {
            $validated['slug'] = $this->generateUniqueSlug($request->nama, $produk->id);
        }

        $stokMapping = ['tersedia' => 'ready', 'po' => 'po', 'habis' => 'habis'];
        $validated['stok'] = $stokMapping[$request->stok];
        $activityType = $produk->stok !== $validated['stok'] ? 'produk.stok_diubah' : 'produk.diedit';

        if ($request->hasFile('foto_utama')) {
            if ($produk->foto_utama) {
                Storage::disk('public')->delete($produk->foto_utama);
            }
            $validated['foto_utama'] = $request->file('foto_utama')->store('produk', 'public');
        }

        // Jika sebelumnya aktif atau ditolak, ubah kembali ke pending
        if (in_array($produk->status, ['aktif', 'ditolak'])) {
            $validated['status'] = 'pending';
        }

        $produk->update($validated);
        $this->recordActivity($activityType);

        $notificationService->notifyAdmins([
            'title' => 'Produk diperbarui',
            'description' => auth()->user()->name . ' mengedit produk "' . $produk->nama . '".',
            'activity' => 'produk.diedit',
            'actor' => auth()->user()->name,
            'entity_type' => 'produk',
            'entity_id' => $produk->id,
            'url' => route('admin.produk.show', $produk),
        ]);

        return redirect()->route('penjual.produk.index')
                         ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Hapus produk.
     */
    public function destroy(Produk $produk, AdminNotificationService $notificationService): RedirectResponse
    {
        $penjualId = $this->getPenjualId();
        abort_unless($produk->penjual_id === $penjualId, 403, 'Akses ditolak.');

        if ($produk->foto_utama) {
            Storage::disk('public')->delete($produk->foto_utama);
        }

        $produkName = $produk->nama;
        $produk->delete();
        $this->recordActivity('produk.dihapus');

        $notificationService->notifyAdmins([
            'title' => 'Produk dihapus',
            'description' => auth()->user()->name . ' menghapus produk "' . $produkName . '".',
            'activity' => 'produk.dihapus',
            'actor' => auth()->user()->name,
            'entity_type' => 'produk',
            'entity_id' => $produk->id,
            'url' => route('admin.produk.index'),
        ]);

        return redirect()->route('penjual.produk.index')
                         ->with('success', 'Produk berhasil dihapus.');
    }
}
