<?php

namespace App\Http\Controllers\Api;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProductController extends ApiController
{
    public function index(Request $request)
    {
        $query = Produk::query()
            ->publicVisible()
            ->with(['kategori', 'penjual'])
            ->withAvg(['ulasan' => fn ($reviews) => $reviews->publicVisible()], 'rating');

        $search = $request->string('search', $request->string('q'))->trim()->toString();
        if ($search !== '') {
            $query->where(function ($products) use ($search) {
                $products->where('nama', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhereHas('penjual', fn ($seller) => $seller->where('nama_toko', 'like', "%{$search}%"));
            });
        }

        if ($category = $request->input('category', $request->input('category_id', $request->input('kategori')))) {
            $query->whereHas('kategori', fn ($categories) => $categories->where('id', $category)->orWhere('slug', $category));
        }

        if ($sellerId = $request->input('seller_id', $request->input('penjual_id'))) {
            $query->where('penjual_id', $sellerId);
        }

        if (is_numeric($request->input('min_price'))) {
            $query->where('harga', '>=', (float) $request->input('min_price'));
        }

        if (is_numeric($request->input('max_price'))) {
            $query->where('harga', '<=', (float) $request->input('max_price'));
        }

        match ($request->input('sort')) {
            'price_asc', 'termurah' => $query->orderBy('harga'),
            'price_desc', 'termahal' => $query->orderByDesc('harga'),
            'popular', 'terpopuler' => $query->orderByDesc('views'),
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
        abort_if($product->status !== 'aktif' || ! ($product->penjual?->user?->is_active), 404);

        $product->increment('views');
        try {
            \App\Models\ProdukViewLog::create([
                'produk_id' => $product->id,
                'type' => 'view',
            ]);
        } catch (\Throwable $e) {
            // Safe fallback
        }

        $product->load(['kategori', 'penjual.user'])->loadAvg(
            ['ulasan' => fn ($reviews) => $reviews->publicVisible()],
            'rating'
        );

        return $this->success($this->productData($product, true), 'Detail produk berhasil diambil');
    }

        public function trackView(Produk $product)
    {
        abort_if($product->status !== 'aktif' || ! ($product->penjual?->user?->is_active), 404);

        $product->increment('views');
        try {
            \App\Models\ProdukViewLog::create([
                'produk_id' => $product->id,
                'type' => 'view',
            ]);
        } catch (\Throwable $e) {
            // Safe fallback
        }

        return $this->success([
            'id' => $product->id,
            'views' => (int) $product->views,
        ], 'View produk berhasil dicatat');
    }

    public function trackWhatsapp(Produk $product)
    {
        abort_if($product->status !== 'aktif' || ! ($product->penjual?->user?->is_active), 404);

        $waNumber = preg_replace('/[^0-9]/', '', $product->penjual->nomor_whatsapp ?? '');
        if (empty($waNumber)) {
            return $this->error('Nomor kontak WhatsApp penjual belum tersedia.', [], 422);
        }

        if (str_starts_with($waNumber, '0')) {
            $waNumber = '62'.substr($waNumber, 1);
        }

        try {
            \App\Models\ProdukViewLog::create([
                'produk_id' => $product->id,
                'type' => 'whatsapp',
            ]);
        } catch (\Throwable $e) {
            // Safe fallback
        }

        $prefixNotice = ($product->penjual->status_toko === 'tutup')
            ? "[Pemberitahuan: Toko sedang tutup, balasan mungkin tertunda]\n\n"
            : '';

        $waMsg = $prefixNotice."Halo, saya tertarik dengan produk:\n*".$product->nama."*\n\nApakah produk ini masih tersedia?";
        $waLink = 'https://wa.me/'.$waNumber.'?text='.rawurlencode($waMsg);

        return $this->success([
            'whatsapp_url' => $waLink,
            'phone' => $waNumber,
            'message' => $waMsg,
        ], 'Tautan WhatsApp berhasil dibuat');
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
                'description' => $product->penjual->deskripsi_toko,
                'class_location' => $product->penjual->lokasi_kelas,
                'location' => null, // Disembunyikan demi privasi siswa
                'store_status' => $product->penjual->status_toko ?? 'buka',
                'phone' => $product->penjual->nomor_whatsapp,
                'image' => $product->penjual->foto_toko ? asset('storage/'.$product->penjual->foto_toko) : null,
            ] : null,
            'rating' => round((float) ($product->ulasan_avg_rating ?? $product->average_rating), 1),
            'review_count' => $product->review_count,
            'views' => (int) ($product->views ?? 0),
            'created_at' => $product->created_at?->toISOString(),
        ];

        if ($detail) {
            $related = Produk::publicVisible()
                ->with(['kategori', 'penjual'])
                ->withAvg(['ulasan' => fn ($q) => $q->publicVisible()], 'rating')
                ->where('kategori_id', $product->kategori_id)
                ->where('id', '!=', $product->id)
                ->latest()
                ->limit(4)
                ->get()
                ->map(fn (Produk $item) => $this->productData($item, false));

            $data['related_products'] = $related;
        }

        return $data;
    }
}
