<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Services\AdminNotificationService;

class ProdukController extends Controller
{
    /**
     * Tampilkan daftar produk untuk dimoderasi.
     */
    public function index(Request $request): View
    {
        $query = Produk::with(['penjual.user', 'kategori'])
            ->latest();

        $status = $request->query('status');
        $validStatuses = ['pending', 'aktif', 'ditolak', 'draft'];

        if ($status && in_array($status, $validStatuses)) {
            $query->where('status', $status);
        }

        $produk = $query->paginate(15)->withQueryString();

        return view('admin.produk.index', compact('produk', 'status'));
    }

    /**
     * Tampilkan detail produk.
     */
    public function show(Produk $produk): View
    {
        $produk->load(['penjual.user', 'kategori']);
        return view('admin.produk.show', compact('produk'));
    }

    /**
     * Update status produk.
     */
    public function updateStatus(Request $request, Produk $produk, AdminNotificationService $notificationService): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:aktif,pending,ditolak',
            'alasan_ditolak' => 'nullable|string|max:500'
        ]);

        $alasan = $validated['status'] === 'ditolak' ? ($validated['alasan_ditolak'] ?? null) : null;

        $produk->update([
            'status' => $validated['status'],
            'alasan_ditolak' => $alasan,
        ]);

        $notificationService->notifyAdmins([
            'title' => 'Status produk diperbarui',
            'description' => 'Status produk "' . $produk->nama . '" diubah menjadi ' . $validated['status'] . '.',
            'activity' => 'produk.status_diubah',
            'actor' => auth()->user()->name,
            'entity_type' => 'produk',
            'entity_id' => $produk->id,
            'url' => route('admin.produk.show', $produk),
        ], auth()->id());

        return redirect()->route('admin.produk.index')->with('success', 'Status produk berhasil diperbarui.');
    }

    /**
     * Hapus produk.
     */
    public function destroy(Produk $produk, AdminNotificationService $notificationService): RedirectResponse
    {
        // Delete image if exists
        if ($produk->foto_utama && \Illuminate\Support\Facades\Storage::disk('public')->exists($produk->foto_utama)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($produk->foto_utama);
        }
        
        $produk->delete();

        $notificationService->notifyAdmins([
            'title' => 'Produk dihapus',
            'description' => 'Produk "' . $produk->nama . '" dihapus oleh ' . auth()->user()->name . '.',
            'activity' => 'produk.dihapus',
            'actor' => auth()->user()->name,
            'entity_type' => 'produk',
            'entity_id' => $produk->id,
            'url' => route('admin.produk.index'),
        ], auth()->id());

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil dihapus.');
    }
}

