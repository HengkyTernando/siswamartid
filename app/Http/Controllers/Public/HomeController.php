<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Penjual;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Tampilkan halaman utama (storefront promosi).
     */
    public function index(): View
    {
        $search = request()->input('q');
        $selectedKategori = request()->input('kategori');

        // 1. Kategori
        $kategoriList = Kategori::withCount([
            'produk as produk_aktif_count' => fn ($query) => $query->where('status', 'aktif'),
        ])->orderBy('nama')->get();

        // Katalog khusus beranda mobile, memakai produk aktif dan filter yang sama dengan katalog publik.
        $produkMobile = Produk::with(['kategori', 'penjual'])
            ->where('status', 'aktif')
            ->when($selectedKategori, fn ($query) => $query->whereHas('kategori', fn ($category) => $category->where('slug', $selectedKategori)))
            ->when($search, function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('nama', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%")
                        ->orWhereHas('penjual', fn ($seller) => $seller->where('nama_toko', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        // 2. Rekomendasi Produk (5 item)
        $produkRekomendasi = Produk::with(['kategori', 'penjual'])
            ->withAvg(['ulasan' => fn($q) => $q->publicVisible()], 'rating')
            ->where('status', 'aktif')
            ->orderByDesc('ulasan_avg_rating')
            ->orderByDesc('views')
            ->latest()
            ->limit(5)
            ->get();
            
        // 3. Produk Populer (Top 3 views)
        $produkPopuler = Produk::with(['kategori', 'penjual'])
            ->where('status', 'aktif')
            ->orderByDesc('views')
            ->limit(3)
            ->get();
            
        // 4. Statistik Dashboard
        $totalProduk = Produk::where('status', 'aktif')->count();
        $totalPenjual = Penjual::count();
        $totalKategori = Kategori::count();

        return view('public.home.index', compact(
            'kategoriList', 
            'produkRekomendasi', 
            'produkPopuler',
            'totalProduk',
            'totalPenjual',
            'totalKategori',
            'produkMobile',
            'search',
            'selectedKategori'
        ));
    }
}
