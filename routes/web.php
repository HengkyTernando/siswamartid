<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Penjual\DashboardController as PenjualDashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ─── Halaman Publik ───────────────────────────────────────────────────────────
Route::get('/', [\App\Http\Controllers\Public\HomeController::class, 'index'])->name('public.home');

Route::get('/kategori', [\App\Http\Controllers\Public\KategoriController::class, 'index'])->name('public.kategori.index');
Route::get('/kategori/{kategori:slug}', [\App\Http\Controllers\Public\KategoriController::class, 'show'])->name('public.kategori.show');

Route::get('/produk', [\App\Http\Controllers\Public\ProdukController::class, 'index'])->name('public.produk.index');
Route::get('/produk/{produk:slug}', [\App\Http\Controllers\Public\ProdukController::class, 'show'])->name('public.produk.show');
Route::get('/produk/{produk:slug}/whatsapp', [\App\Http\Controllers\Public\ProdukController::class, 'trackWhatsapp'])->name('public.produk.whatsapp');

Route::view('/cara-pemesanan', 'public.cara-pemesanan')->name('public.cara_pemesanan');
Route::view('/tentang-kami', 'public.tentang-kami')->name('public.tentang_kami');

// Submit ulasan dengan throttle (3 request / 1 menit)
Route::post('/produk/{produk:slug}/ulasan', [\App\Http\Controllers\Public\UlasanController::class, 'store'])
    ->name('public.ulasan.store')
    ->middleware('throttle:3,1');

// ─── Admin Area ───────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::post('/notifikasi/baca-semua', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllRead'])->name('notifications.read_all');
    Route::get('/notifikasi/poll', [\App\Http\Controllers\Admin\NotificationController::class, 'poll'])->name('notifications.poll');
    Route::get('/notifikasi/{notification}/baca', [\App\Http\Controllers\Admin\NotificationController::class, 'markRead'])->name('notifications.read');

    // Manajemen Penjual
    Route::resource('penjual', \App\Http\Controllers\Admin\PenjualController::class)
        ->parameters(['penjual' => 'penjual']);
    Route::patch('penjual/{penjual}/toggle-status', [\App\Http\Controllers\Admin\PenjualController::class, 'toggleStatus'])
        ->name('penjual.toggleStatus');

    // Manajemen Kategori
    Route::resource('categories', \App\Http\Controllers\Admin\KategoriController::class)->only(['index', 'store', 'update', 'destroy']);

    // Moderasi Produk
    Route::get('produk', [\App\Http\Controllers\Admin\ProdukController::class, 'index'])->name('produk.index');
    Route::get('produk/{produk}', [\App\Http\Controllers\Admin\ProdukController::class, 'show'])->name('produk.show');
    Route::patch('produk/{produk}/update-status', [\App\Http\Controllers\Admin\ProdukController::class, 'updateStatus'])->name('produk.updateStatus');
    Route::delete('produk/{produk}', [\App\Http\Controllers\Admin\ProdukController::class, 'destroy'])->name('produk.destroy');

    // Kelola Komentar & Auto Moderasi
    Route::get('komentar', [\App\Http\Controllers\Admin\KomentarController::class, 'index'])->name('komentar.index');
    Route::patch('komentar/{ulasan}', [\App\Http\Controllers\Admin\KomentarController::class, 'update'])->name('komentar.update');
    Route::patch('komentar/{ulasan}/approve', [\App\Http\Controllers\Admin\KomentarController::class, 'approve'])->name('komentar.approve');
    Route::patch('komentar/{ulasan}/censor', [\App\Http\Controllers\Admin\KomentarController::class, 'censor'])->name('komentar.censor');
    Route::patch('komentar/{ulasan}/reject', [\App\Http\Controllers\Admin\KomentarController::class, 'reject'])->name('komentar.reject');
    Route::patch('komentar/{ulasan}/hide', [\App\Http\Controllers\Admin\KomentarController::class, 'hide'])->name('komentar.hide');
    Route::delete('komentar/{ulasan}', [\App\Http\Controllers\Admin\KomentarController::class, 'destroy'])->name('komentar.destroy');

    // Filter Kata (Bad Words)
    Route::post('komentar/bad-words', [\App\Http\Controllers\Admin\KomentarController::class, 'storeBadWord'])->name('komentar.badWords.store');
    Route::patch('komentar/bad-words/{badWord}', [\App\Http\Controllers\Admin\KomentarController::class, 'updateBadWord'])->name('komentar.badWords.update');
    Route::patch('komentar/bad-words/{badWord}/toggle', [\App\Http\Controllers\Admin\KomentarController::class, 'toggleBadWord'])->name('komentar.badWords.toggle');
    Route::delete('komentar/bad-words/{badWord}', [\App\Http\Controllers\Admin\KomentarController::class, 'destroyBadWord'])->name('komentar.badWords.destroy');

    // Whitelist Words/Phrases
    Route::post('komentar/whitelists', [\App\Http\Controllers\Admin\KomentarController::class, 'storeWhitelist'])->name('komentar.whitelists.store');
    Route::patch('komentar/whitelists/{whitelist}/toggle', [\App\Http\Controllers\Admin\KomentarController::class, 'toggleWhitelist'])->name('komentar.whitelists.toggle');
    Route::delete('komentar/whitelists/{whitelist}', [\App\Http\Controllers\Admin\KomentarController::class, 'destroyWhitelist'])->name('komentar.whitelists.destroy');

    // Alias untuk ulasan.index
    Route::get('ulasan', [\App\Http\Controllers\Admin\KomentarController::class, 'index'])->name('ulasan.index');
    Route::patch('ulasan/{ulasan}/approve', [\App\Http\Controllers\Admin\KomentarController::class, 'approve'])->name('ulasan.approve');
    Route::patch('ulasan/{ulasan}/reject', [\App\Http\Controllers\Admin\KomentarController::class, 'reject'])->name('ulasan.reject');
});

// ─── Penjual Area ─────────────────────────────────────────────────────────────
Route::prefix('penjual')->name('penjual.')->middleware(['auth', 'penjual'])->group(function () {
    Route::get('/dashboard', [PenjualDashboardController::class, 'index'])->name('dashboard');
    Route::get('/laporan', [\App\Http\Controllers\Penjual\LaporanController::class, 'index'])->name('laporan');

    // Manajemen Produk
    Route::resource('produk', \App\Http\Controllers\Penjual\ProdukController::class)
        ->parameters(['produk' => 'produk']);
});

// ─── Profile (Breeze default, auth only) ─────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
