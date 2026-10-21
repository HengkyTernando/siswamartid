<?php

namespace App\Http\Controllers\Api;

use App\Models\Penjual;
use App\Models\Produk;
use App\Models\Ulasan;
use Illuminate\Http\Request;

class SellerController extends ApiController
{
    public function index()
    {
        $sellers = Penjual::query()
            ->whereHas('user', fn ($q) => $q->where('is_active', true))
            ->withCount(['produk as active_product_count' => fn ($q) => $q->publicVisible()])
            ->orderBy('nama_toko')
            ->get()
            ->map(fn (Penjual $seller) => $this->sellerData($seller));

        return $this->success($sellers, 'Daftar penjual berhasil diambil');
    }

    public function show(Penjual $seller, ProductController $productController)
    {
        abort_unless($seller->user?->is_active, 404);

        $products = $seller->produk()
            ->publicVisible()
            ->with(['kategori', 'penjual'])
            ->withAvg(['ulasan' => fn ($reviews) => $reviews->publicVisible()], 'rating')
            ->latest()
            ->get()
            ->map(fn (Produk $product) => $productController->productData($product));

        $data = $this->sellerData($seller);
        $data['products'] = $products;

        return $this->success($data, 'Profil penjual berhasil diambil');
    }

    public function dashboard(Request $request, ProductController $productController)
    {
        $user = $request->user();
        $penjual = $user?->penjual;

        if (! $penjual) {
            return $this->error('Profil penjual tidak ditemukan untuk akun ini.', [], 403);
        }

        $produkQuery = Produk::where('penjual_id', $penjual->id);
        $totalProduk = (clone $produkQuery)->count();
        $produkAktif = (clone $produkQuery)->where('status', 'aktif')->count();
        $produkPending = (clone $produkQuery)->where('status', 'pending')->count();
        $produkDitolak = (clone $produkQuery)->where('status', 'ditolak')->count();
        $totalViews = (int) (clone $produkQuery)->sum('views');
        $totalUlasan = Ulasan::whereIn('produk_id', (clone $produkQuery)->select('id'))
            ->publicVisible()
            ->count();

        $recentProducts = Produk::with(['kategori', 'penjual'])
            ->where('penjual_id', $penjual->id)
            ->latest()
            ->take(10)
            ->get()
            ->map(fn (Produk $p) => array_merge($productController->productData($p), [
                'moderation_status' => $p->status,
                'pending_action' => $p->pending_action,
                'rejection_reason' => $p->alasan_ditolak,
            ]));

        return $this->success([
            'seller' => $this->sellerData($penjual),
            'stats' => [
                'total_products' => $totalProduk,
                'active_products' => $produkAktif,
                'pending_products' => $produkPending,
                'rejected_products' => $produkDitolak,
                'total_views' => $totalViews,
                'total_reviews' => $totalUlasan,
            ],
            'products' => $recentProducts,
        ], 'Dashboard penjual berhasil diambil');
    }

    public function toggleStoreStatus(Request $request)
    {
        $penjual = $request->user()?->penjual;
        if (! $penjual) {
            return $this->error('Profil penjual tidak ditemukan.', [], 403);
        }

        $newStatus = ($penjual->status_toko === 'tutup') ? 'buka' : 'tutup';
        $penjual->update(['status_toko' => $newStatus]);

        return $this->success(
            $this->sellerData($penjual->fresh()),
            "Status toko berhasil diubah menjadi {$newStatus}."
        );
    }

    public function sellerData(Penjual $seller): array
    {
        return [
            'id' => $seller->id,
            'name' => $seller->nama_toko,
            'description' => $seller->deskripsi_toko,
            'class_location' => $seller->lokasi_kelas,
            'location' => null,
            'store_status' => $seller->status_toko ?? 'buka',
            'phone' => $seller->nomor_whatsapp,
            'image' => $seller->foto_toko ? asset('storage/'.$seller->foto_toko) : null,
            'active_product_count' => $seller->active_product_count ?? $seller->produk()->publicVisible()->count(),
        ];
    }
}
