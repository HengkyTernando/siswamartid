@extends('layouts.admin')

@section('title', 'Rekap Admin')
@section('page-title', 'Rekap Admin')

@section('content')
<div class="space-y-6">
    <section class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <h2 class="text-2xl font-black text-[var(--text-main)]">Rekap {{ $kategoriLaporan === 'penjual' ? 'Kinerja Penjual' : 'Ulasan' }}</h2>
            <p class="mt-1 text-sm text-[var(--text-muted)]">Periode {{ $start->format('d M Y') }}–{{ $end->format('d M Y') }}.</p>
        </div>
        <form method="GET" action="{{ route('admin.laporan.index') }}" class="flex flex-wrap items-center gap-2">
            <input type="hidden" name="kategori" value="{{ $kategoriLaporan }}">
            <label class="sr-only" for="report-period">Periode rekap</label>
            <select id="report-period" name="period" class="form-input min-h-11 w-auto">
                <option value="weekly" @selected($period === 'weekly')>Mingguan</option>
                <option value="monthly" @selected($period === 'monthly')>Bulanan</option>
            </select>
            <button type="submit" class="btn-primary min-h-11">Tampilkan</button>
            <a href="{{ route('admin.laporan.penjual.pdf', ['period' => $period]) }}"
               class="{{ $kategoriLaporan === 'penjual' ? 'btn-primary' : 'btn-outline' }} min-h-11">
                <x-public.home-icon name="download" size="16" class="inline-block align-[-3px]" aria-hidden="true" /> Unduh PDF Penjual
            </a>
            <a href="{{ route('admin.laporan.ulasan.pdf', ['period' => $period]) }}"
               class="{{ $kategoriLaporan === 'ulasan' ? 'btn-primary' : 'btn-outline' }} min-h-11">
                <x-public.home-icon name="download" size="16" class="inline-block align-[-3px]" aria-hidden="true" /> Unduh PDF Ulasan
            </a>
        </form>
    </section>

    <nav class="admin-card flex flex-wrap gap-2 p-3" aria-label="Kategori rekap">
        <a href="{{ route('admin.laporan.index', ['kategori' => 'penjual', 'period' => $period]) }}"
           class="{{ $kategoriLaporan === 'penjual' ? 'btn-primary' : 'btn-outline' }} min-h-11" aria-current="{{ $kategoriLaporan === 'penjual' ? 'page' : 'false' }}">
            Performa Penjual
        </a>
        <a href="{{ route('admin.laporan.index', ['kategori' => 'ulasan', 'period' => $period]) }}"
           class="{{ $kategoriLaporan === 'ulasan' ? 'btn-primary' : 'btn-outline' }} min-h-11" aria-current="{{ $kategoriLaporan === 'ulasan' ? 'page' : 'false' }}">
            Rekap Ulasan
        </a>
    </nav>

    @if($kategoriLaporan === 'penjual')
        <section class="space-y-5" aria-label="Performa penjual">
            <div class="grid grid-cols-2 gap-4 xl:grid-cols-6">
                <div class="stat-card p-4"><p class="text-xs text-[var(--text-muted)]">Total penjual</p><p class="mt-1 text-2xl font-black">{{ number_format($sellerReport['total_sellers']) }}</p></div>
                <div class="stat-card p-4"><p class="text-xs text-[var(--text-muted)]">Produk ditambahkan</p><p class="mt-1 text-2xl font-black text-emerald-700">{{ number_format($sellerReport['products_added']) }}</p></div>
                <div class="stat-card p-4"><p class="text-xs text-[var(--text-muted)]">Produk dihapus</p><p class="mt-1 text-2xl font-black text-rose-700">{{ number_format($sellerReport['products_deleted']) }}</p></div>
                <div class="stat-card p-4"><p class="text-xs text-[var(--text-muted)]">Permintaan edit</p><p class="mt-1 text-2xl font-black">{{ number_format($sellerReport['edits_requested']) }}</p></div>
                <div class="stat-card p-4"><p class="text-xs text-[var(--text-muted)]">Dilihat</p><p class="mt-1 text-2xl font-black">{{ number_format($sellerReport['views']) }}</p></div>
                <div class="stat-card p-4"><p class="text-xs text-[var(--text-muted)]">Ulasan baru</p><p class="mt-1 text-2xl font-black">{{ number_format($sellerReport['reviews']) }}</p></div>
            </div>

            <section class="admin-card">
                <div class="admin-card-header"><div><h3 class="font-black">Performa setiap penjual</h3><p class="mt-1 text-xs text-[var(--text-muted)]">Urutan berdasarkan kunjungan produk selama periode yang dipilih.</p></div></div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[920px] text-left text-sm">
                        <thead class="bg-[#FFEBCB] text-xs uppercase tracking-wide">
                            <tr><th class="px-5 py-3">Penjual</th><th class="px-4 py-3">Produk aktif / total</th><th class="px-4 py-3">Dilihat</th><th class="px-4 py-3">Ulasan</th><th class="px-4 py-3">Rating</th><th class="px-4 py-3">Tambah</th><th class="px-4 py-3">Hapus</th><th class="px-4 py-3">Minta edit</th></tr>
                        </thead>
                        <tbody>
                            @forelse($sellerReport['sellers'] as $seller)
                                <tr class="border-t border-[var(--border-color)]/15">
                                    <td class="px-5 py-3"><p class="font-bold">{{ $seller->nama_toko }}</p><p class="text-xs text-[var(--text-muted)]">{{ $seller->user?->name ?? 'Akun penjual' }}</p></td>
                                    <td class="px-4 py-3">{{ $seller->produk_aktif_count ?? 0 }} / {{ $seller->produk_count }}</td>
                                    <td class="px-4 py-3 font-bold">{{ number_format($seller->period_views) }}</td>
                                    <td class="px-4 py-3">{{ number_format($seller->period_reviews) }}</td>
                                    <td class="px-4 py-3">{{ $seller->period_rating !== null ? number_format($seller->period_rating, 1) . ' / 5' : '—' }}</td>
                                    <td class="px-4 py-3 text-emerald-700">{{ $seller->period_added }}</td>
                                    <td class="px-4 py-3 text-rose-700">{{ $seller->period_deleted }}</td>
                                    <td class="px-4 py-3 font-bold">{{ number_format($seller->period_edits_requested) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="px-5 py-10 text-center text-[var(--text-muted)]">Belum ada data penjual.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">
                <section class="admin-card">
                    <div class="admin-card-header"><div><h3 class="font-black">Produk paling banyak dilihat</h3><p class="mt-1 text-xs text-[var(--text-muted)]">Kunjungan tercatat pada periode ini.</p></div></div>
                    <div class="divide-y divide-[var(--border-color)]/15 px-5">
                        @forelse($sellerReport['top_products'] as $product)
                            <div class="flex items-center justify-between gap-4 py-3 text-sm">
                                <div class="min-w-0"><p class="truncate font-bold">{{ $product->nama }}</p><p class="truncate text-xs text-[var(--text-muted)]">{{ $product->penjual?->nama_toko ?? 'Penjual tidak tersedia' }} · {{ $product->period_reviews }} ulasan</p></div>
                                <div class="shrink-0 text-right"><p class="font-black">{{ number_format($product->period_views) }} dilihat</p></div>
                            </div>
                        @empty
                            <p class="py-8 text-center text-sm text-[var(--text-muted)]">Belum ada kunjungan produk pada periode ini.</p>
                        @endforelse
                    </div>
                </section>

                <section class="admin-card">
                    <div class="admin-card-header"><div><h3 class="font-black">Aktivitas produk</h3><p class="mt-1 text-xs text-[var(--text-muted)]">Penambahan dan penghapusan yang tercatat pada periode ini.</p></div></div>
                    <div class="divide-y divide-[var(--border-color)]/15 px-5">
                        @forelse($sellerReport['events'] as $event)
                            <div class="flex items-start justify-between gap-4 py-3 text-sm">
                                <div><p class="font-bold">{{ $event['description'] }}</p><p class="text-xs text-[var(--text-muted)]">{{ $event['seller_name'] }} · {{ match($event['activity']) { 'produk.ditambahkan' => 'Produk ditambahkan', 'produk.dihapus' => 'Produk dihapus', default => 'Permintaan edit produk' } }}</p></div>
                                <time class="shrink-0 text-xs text-[var(--text-muted)]">{{ $event['created_at']->format('d M, H:i') }}</time>
                            </div>
                        @empty
                            <p class="py-8 text-center text-sm text-[var(--text-muted)]">Belum ada aktivitas tambah/hapus yang tercatat pada periode ini.</p>
                        @endforelse
                    </div>
                    <p class="border-t border-[var(--border-color)]/15 px-5 py-3 text-xs text-[var(--text-muted)]">Aktivitas tambah, hapus, dan permintaan edit dihitung dari notifikasi yang tersimpan. Riwayat kunjungan produk yang sudah dihapus tidak tersedia karena log ikut terhapus.</p>
                </section>
            </div>
        </section>
    @else
        <section class="space-y-5" aria-label="Rekap ulasan">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="stat-card p-4"><p class="text-xs text-[var(--text-muted)]">Ulasan diterbitkan</p><p class="mt-1 text-2xl font-black">{{ number_format($reviewReport['total']) }}</p></div>
                <div class="stat-card p-4"><p class="text-xs text-[var(--text-muted)]">Rating rata-rata</p><p class="mt-1 text-2xl font-black text-amber-700">{{ number_format($reviewReport['average'], 2) }} / 5</p></div>
                <div class="stat-card p-4">
                    <p class="text-xs text-[var(--text-muted)]">Sebaran rating</p>
                    <div class="mt-2 grid grid-cols-1 gap-1.5">
                        @foreach($reviewReport['distribution'] as $stars => $count)
                            <div class="flex items-center justify-between gap-4 text-sm font-bold">
                                <span class="inline-flex items-center gap-2"><x-public.home-icon name="star" :size="16" :filled="true" class="h-4 w-4 text-amber-500" />{{ $stars }} bintang</span>
                                <span class="tabular-nums">{{ number_format($count) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <section class="admin-card">
                <div class="admin-card-header"><div><h3 class="font-black">Produk dengan ulasan terbanyak</h3><p class="mt-1 text-xs text-[var(--text-muted)]">Termasuk nilai rating rata-rata pada periode ini.</p></div></div>
                <div class="divide-y divide-[var(--border-color)]/15 px-5">
                    @forelse($reviewReport['top_products'] as $product)
                        <div class="flex items-center justify-between gap-4 py-3 text-sm">
                            <div><p class="font-bold">{{ $product->nama }}</p><p class="text-xs text-[var(--text-muted)]">{{ $product->penjual?->nama_toko ?? 'Penjual tidak tersedia' }}</p></div>
                            <div class="shrink-0 text-right"><p class="font-black">{{ $product->period_reviews }} ulasan</p><p class="text-xs text-amber-700">{{ number_format((float) $product->period_rating, 1) }} / 5</p></div>
                        </div>
                    @empty
                        <p class="py-8 text-center text-sm text-[var(--text-muted)]">Belum ada ulasan pada periode ini.</p>
                    @endforelse
                </div>
            </section>
        </section>
    @endif
</div>
@endsection
