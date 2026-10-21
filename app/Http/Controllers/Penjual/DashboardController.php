<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Ulasan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard Penjual.
     */
    public function index(): View
    {
        $penjual = auth()->user()->penjual;
        abort_unless($penjual, 403, 'Profil penjual tidak ditemukan. Silakan hubungi Admin.');
        $penjualId = $penjual->id;

        $produkQuery = Produk::where('penjual_id', $penjualId);
        $totalProduk = (clone $produkQuery)->count();
        $produkAktif = (clone $produkQuery)->where('status', 'aktif')->count();
        $produkPending = (clone $produkQuery)->where('status', 'pending')->count();
        $totalViews = (clone $produkQuery)->sum('views');
        $totalUlasan = Ulasan::whereIn('produk_id', (clone $produkQuery)->select('id'))
            ->publicVisible()
            ->count();

        $topProduk = Produk::where('penjual_id', $penjualId)
            ->withCount(['ulasan as ulasan_count' => fn ($query) => $query->publicVisible()])
            ->orderByDesc('views')
            ->take(3)
            ->get();

        $terbaru = \App\Models\Produk::with('kategori')
            ->where('penjual_id', $penjualId)
            ->latest()
            ->take(5)
            ->get();

        return view('penjual.dashboard', compact(
            'penjual', 'totalProduk', 'produkAktif', 'produkPending', 'totalViews', 'totalUlasan', 'topProduk', 'terbaru'
        ));
    }

    /**
     * Ubah status toko penjual (buka / tutup).
     */
    public function toggleStatusToko(Request $request): RedirectResponse
    {
        $penjual = auth()->user()->penjual;
        abort_unless($penjual, 403, 'Profil penjual tidak ditemukan.');

        $newStatus = ($penjual->status_toko === 'tutup') ? 'buka' : 'tutup';
        $penjual->update(['status_toko' => $newStatus]);

        return back()->with('success', "Status toko berhasil diubah menjadi: {$newStatus}.");
    }
}
