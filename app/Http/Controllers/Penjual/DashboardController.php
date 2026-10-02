<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
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

        $totalProduk = \App\Models\Produk::where('penjual_id', $penjualId)->count();
        $produkAktif = \App\Models\Produk::where('penjual_id', $penjualId)->where('status', 'aktif')->count();
        $produkPending = \App\Models\Produk::where('penjual_id', $penjualId)->where('status', 'pending')->count();
        $totalViews = \App\Models\Produk::where('penjual_id', $penjualId)->sum('views');

        $topProduk = \App\Models\Produk::where('penjual_id', $penjualId)
            ->orderByDesc('views')
            ->take(3)
            ->get();

        $terbaru = \App\Models\Produk::with('kategori')
            ->where('penjual_id', $penjualId)
            ->latest()
            ->take(5)
            ->get();

        return view('penjual.dashboard', compact(
            'totalProduk', 'produkAktif', 'produkPending', 'totalViews', 'topProduk', 'terbaru'
        ));
    }
}
