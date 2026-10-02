<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KategoriController extends Controller
{
    /**
     * Tampilkan daftar semua kategori (Semua Produk) dengan layout katalog.
     */
    public function index(Request $request): View
    {
        $search = $request->input('q');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $sort = $request->input('sort', 'terbaru');

        $kategoriList = Kategori::withCount(['produk' => function ($q) {
            $q->where('status', 'aktif');
        }])->orderBy('nama')->get();

        $totalSemua = Produk::where('status', 'aktif')->count();

        $produkQuery = Produk::with(['kategori', 'penjual'])->where('status', 'aktif');

        if ($search) {
            $produkQuery->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if (is_numeric($minPrice)) {
            $produkQuery->where('harga', '>=', $minPrice);
        }
        if (is_numeric($maxPrice)) {
            $produkQuery->where('harga', '<=', $maxPrice);
        }

        switch ($sort) {
            case 'termurah':
                $produkQuery->orderBy('harga', 'asc');
                break;
            case 'termahal':
                $produkQuery->orderBy('harga', 'desc');
                break;
            case 'terpopuler':
                $produkQuery->orderBy('views', 'desc');
                break;
            case 'terbaru':
            default:
                $produkQuery->latest();
                break;
        }

        $produk = $produkQuery->paginate(12)->withQueryString();

        return view('public.kategori.index', compact('kategoriList', 'produk', 'search', 'totalSemua', 'minPrice', 'maxPrice', 'sort'));
    }

    /**
     * Tampilkan produk untuk kategori tertentu dengan layout katalog.
     */
    public function show(Kategori $kategori, Request $request): View
    {
        $search = $request->input('q');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $sort = $request->input('sort', 'terbaru');

        $kategoriList = Kategori::withCount(['produk' => function ($q) {
            $q->where('status', 'aktif');
        }])->orderBy('nama')->get();

        $totalSemua = Produk::where('status', 'aktif')->count();

        $produkQuery = Produk::with(['kategori', 'penjual'])
            ->where('kategori_id', $kategori->id)
            ->where('status', 'aktif');

        if ($search) {
            $produkQuery->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if (is_numeric($minPrice)) {
            $produkQuery->where('harga', '>=', $minPrice);
        }
        if (is_numeric($maxPrice)) {
            $produkQuery->where('harga', '<=', $maxPrice);
        }

        switch ($sort) {
            case 'termurah':
                $produkQuery->orderBy('harga', 'asc');
                break;
            case 'termahal':
                $produkQuery->orderBy('harga', 'desc');
                break;
            case 'terpopuler':
                $produkQuery->orderBy('views', 'desc');
                break;
            case 'terbaru':
            default:
                $produkQuery->latest();
                break;
        }

        $produk = $produkQuery->paginate(12)->withQueryString();

        return view('public.kategori.show', compact('kategoriList', 'kategori', 'produk', 'search', 'totalSemua', 'minPrice', 'maxPrice', 'sort'));
    }
}
