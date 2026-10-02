<?php

namespace App\Http\Controllers\Api;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProductController extends ApiController
{
    public function index(Request $request)
    {
        $query = Produk::query()
            ->with(['kategori', 'penjual'])
            ->withAvg(['ulasan' => fn ($reviews) => $reviews->publicVisible()], 'rating')
            ->where('status', 'aktif');

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($products) use ($search) {
                $products->where('nama', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category', $request->input('category_id'))) {
            $query->whereHas('kategori', fn ($categories) => $categories->where('id', $category)->orWhere('slug', $category));
        }

        match ($request->input('sort')) {
            'price_asc' => $query->orderBy('harga'),
            'price_desc' => $query->orderByDesc('harga'),
            'rating' => $query->orderByDesc('ulasan_avg_rating'),
            default => $query->latest(),
        };

        $perPage = min(max((int) $request->input('per_page', 12), 1), 50);
        $products = $query->paginate($perPage)->withQueryString();
        $products->through(fn (Produk $product) => $this->productData($product));

        return $this->success($products, 'Daftar produk berhasil diambil');
    }

    public function show(Produk $product)
    {
        abort_if($product->status !== 'aktif', 404);

        $product->load(['kategori', 'penjual.user'])->loadAvg(
            ['ulasan' => fn ($reviews) => $reviews->publicVisible()],
            'rating'
        );

        return $this->success($this->productData($product, true), 'Detail produk berhasil diambil');
    }

    public function productData(Produk $product, bool $detail = false): array
    {
        $data = [
            'id' => $product->id,
            'name' => $product->nama,
            'slug' => $product->slug,
            'description' => $product->deskripsi,
            'price' => (float) $product->harga,
            'stock_status' => $product->stok,
            'available' => $product->stok !== 'habis',
            'promotion_label' => $product->label_promosi,
            'image' => $product->foto_utama ? asset('storage/'.$product->foto_utama) : null,
            'category' => $product->kategori ? [
                'id' => $product->kategori->id,
                'name' => $product->kategori->nama,
                'slug' => $product->kategori->slug,
                'icon' => $product->kategori->icon,
            ] : null,
            'seller' => $product->penjual ? [
                'id' => $product->penjual->id,
                'name' => $product->penjual->nama_toko,
                'location' => $product->penjual->lokasi_kelas,
                'phone' => $product->penjual->nomor_whatsapp,
                'image' => $product->penjual->foto_toko ? asset('storage/'.$product->penjual->foto_toko) : null,
            ] : null,
            'rating' => round((float) ($product->ulasan_avg_rating ?? $product->average_rating), 1),
            'review_count' => $product->review_count,
        ];

        if ($detail) {
            $data['views'] = $product->views;
        }

        return $data;
    }
}
