<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Penjual;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProdukController extends Controller
{
    /**
     * Tampilkan katalog produk untuk pengunjung.
     */
    public function index(Request $request): View
    {
        $search = $request->input('q');
        $selectedKategori = $request->input('kategori');
        $sort = $request->input('sort', 'terbaru');

        $kategoriList = Kategori::withCount(['produk' => fn ($query) => $query->where('status', 'aktif')])
            ->orderBy('nama')
            ->get();

        $produkQuery = Produk::with(['kategori', 'penjual'])
            ->where('status', 'aktif')
            ->when($selectedKategori, fn ($query) => $query->whereHas('kategori', fn ($category) => $category->where('slug', $selectedKategori)))
            ->when($search, function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('nama', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%")
                        ->orWhereHas('penjual', fn ($seller) => $seller->where('nama_toko', 'like', "%{$search}%"));
                });
            });

        match ($sort) {
            'termurah' => $produkQuery->orderBy('harga'),
            'termahal' => $produkQuery->orderByDesc('harga'),
            'terpopuler' => $produkQuery->orderByDesc('views'),
            default => $produkQuery->latest(),
        };

        $produk = $produkQuery->paginate(12)->withQueryString();

        return view('public.produk.index', compact('produk', 'kategoriList', 'search', 'selectedKategori', 'sort'));
    }

    /**
     * Tampilkan detail produk.
     * Hanya produk aktif yang dapat diakses; increment views untuk produk aktif.
     */
    public function show(Produk $produk): View
    {
        // Pastikan hanya produk aktif yang bisa diakses
        abort_if($produk->status !== 'aktif', 404);

        // Tambah counter view (hanya produk aktif, dari halaman public)
        $produk->increment('views');
        try {
            \App\Models\ProdukViewLog::create([
                'produk_id' => $produk->id,
                'type'      => 'view',
            ]);
        } catch (\Throwable $e) {
            // Safe fallback
        }

        $produk->load(['kategori', 'penjual.user']);

        // Ulasan publik (aktif/disetujui/disensor)
        $ulasan = $produk->ulasan()
            ->publicVisible()
            ->latest()
            ->get();

        // Produk Terkait (kategori sama, aktif, tidak termasuk diri sendiri)
        $produkTerkait = Produk::with(['kategori', 'penjual'])
            ->withAvg(['ulasan' => fn($q) => $q->publicVisible()], 'rating')
            ->where('kategori_id', $produk->kategori_id)
            ->where('id', '!=', $produk->id)
            ->where('status', 'aktif')
            ->latest()
            ->limit(4)
            ->get();

        return view('public.produk.show', compact('produk', 'ulasan', 'produkTerkait'));
    }

    /**
     * Catat interaksi klik WhatsApp dan alihkan ke WhatsApp penjual.
     */
    public function trackWhatsapp(Produk $produk): \Illuminate\Http\RedirectResponse
    {
        abort_if($produk->status !== 'aktif', 404);

        try {
            \App\Models\ProdukViewLog::create([
                'produk_id' => $produk->id,
                'type'      => 'whatsapp',
            ]);
        } catch (\Throwable $e) {
            // Safe fallback
        }

        $waNumber = $produk->penjual->nomor_whatsapp ?? '';
        if (str_starts_with($waNumber, '0')) {
            $waNumber = '62' . substr($waNumber, 1);
        } elseif (str_starts_with($waNumber, '+')) {
            $waNumber = substr($waNumber, 1);
        }
        $waMsg  = "Halo, saya tertarik dengan produk:\n*" . $produk->nama . "*\n\nApakah produk ini masih tersedia?";
        $waLink = "https://wa.me/" . $waNumber . "?text=" . rawurlencode($waMsg);

        return redirect()->away($waLink);
    }
}
