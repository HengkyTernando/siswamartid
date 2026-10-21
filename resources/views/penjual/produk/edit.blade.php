@extends('layouts.penjual')

@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk')

@section('content')

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('penjual.produk.index') }}" class="p-2 rounded-full hover:bg-[var(--bg-hover)] text-[var(--text-muted)] transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h2 class="text-xl font-bold text-[var(--text-main)]">Edit Produk</h2>
    </div>

    @if($produk->pending_action)
        <div class="mb-6 rounded-2xl border-2 border-[var(--ink)] bg-[var(--warning-bg)] p-4 shadow-[3px_3px_0_var(--ink)]">
            <p class="text-sm font-bold text-[var(--ink)]">Pengajuan menunggu persetujuan Admin.</p>
            <p class="mt-1 text-xs text-[var(--ink-soft)]">Kamu belum bisa mengirim perubahan lain sebelum Admin memutuskan pengajuan ini.</p>
        </div>
    @elseif($produk->alasan_ditolak && $produk->status !== 'ditolak')
        <div class="mb-6 rounded-2xl border-2 border-[var(--ink)] bg-[var(--warning-bg)] p-4 shadow-[3px_3px_0_var(--ink)]">
            <p class="text-sm font-bold text-[var(--ink)]">Pengajuan sebelumnya ditolak.</p>
            <p class="mt-1 text-xs text-[var(--ink-soft)]">Catatan Admin: {{ $produk->alasan_ditolak }}</p>
        </div>
    @elseif($produk->status === 'ditolak')
        <div class="bg-[#fce8e6] border border-[#ea4335] rounded-2xl p-4 mb-6 shadow-sm">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-[#ea4335] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <p class="text-sm font-bold text-[#c5221f]">Produk Ditolak</p>
                    <p class="text-xs text-[#c5221f]/90 mt-1 mb-2">Produkmu ditolak Admin. Perbaiki sesuai catatan berikut, lalu kirim lagi untuk ditinjau.</p>
                    <div class="text-sm bg-white/50 px-3 py-2 rounded-lg border border-[#ea4335]/30 text-[#c5221f] italic">
                        "{{ $produk->alasan_ditolak ?? 'Tidak ada alasan spesifik yang diberikan.' }}"
                    </div>
                </div>
            </div>
        </div>
    @elseif($produk->status === 'pending')
        <div class="bg-[#fef7e0] border border-[#fbbc04] rounded-2xl p-4 mb-6 shadow-sm">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-[#b06000] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <p class="text-sm font-bold text-[#b06000]">Menunggu Moderasi</p>
                    <p class="text-xs text-[#b06000]/90 mt-0.5">Produkmu sedang ditinjau. Setiap perubahan yang kamu ajukan akan menunggu persetujuan Admin.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="admin-card p-6">
                <form method="POST" action="{{ route('penjual.produk.update', $produk) }}" enctype="multipart/form-data" class="space-y-6" id="produkForm">
                    @csrf @method('PUT')
                    
                    <h3 class="text-sm font-bold text-[var(--text-main)] border-b border-[var(--border-color)] pb-2 mb-4 uppercase tracking-widest">Informasi Utama</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Nama Produk</label>
                        <input type="text" name="nama" value="{{ old('nama', $produk->nama) }}" required class="form-input">
                        @error('nama') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Kategori</label>
                            <select name="kategori_id" required class="form-input">
                                @foreach($kategori as $k)
                                    <option value="{{ $k->id }}" {{ (old('kategori_id', $produk->kategori_id) == $k->id) ? 'selected' : '' }}>{{ $k->nama }}</option>
                                @endforeach
                            </select>
                            @error('kategori_id') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Harga (Rp)</label>
                            <input type="number" name="harga" value="{{ old('harga', $produk->harga) }}" required min="0" class="form-input">
                            @error('harga') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Deskripsi Produk</label>
                        <textarea name="deskripsi" rows="5" required class="form-input">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                        @error('deskripsi') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <h3 class="text-sm font-bold text-[var(--text-main)] border-b border-[var(--border-color)] pb-2 mt-8 mb-4 uppercase tracking-widest">Media & Status</h3>
                    
                    <div class="flex flex-col sm:flex-row gap-6 items-start">
                        @if($produk->foto_utama)
                            <div class="w-24 h-24 rounded-lg overflow-hidden border border-[var(--border-color)] flex-shrink-0">
                                <img src="{{ asset('storage/' . $produk->foto_utama) }}" alt="Preview" class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="w-24 h-24 rounded-lg bg-[var(--bg-hover)] border border-[var(--border-color)] flex-shrink-0 flex items-center justify-center text-xs text-[var(--text-muted)] text-center p-2">
                                Tidak ada foto
                            </div>
                        @endif
                        
                        <div class="flex-1 w-full">
                            <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Ganti Foto <span class="text-xs text-[var(--text-muted)] font-normal">(Opsional)</span></label>
                            <input type="file" name="foto_utama" accept="image/*" class="w-full text-sm text-[var(--text-muted)] file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[var(--bg-active)] file:text-[#1a73e8] hover:file:bg-[#d2e3fc] transition cursor-pointer">
                            <p class="text-xs text-[var(--text-muted)] mt-1.5">Kosongkan jika fotonya tetap. Format: JPG, PNG, atau WEBP.</p>
                            @error('foto_utama') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Status Ketersediaan</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="stok" value="tersedia" {{ old('stok', $produk->stok) === 'tersedia' ? 'checked' : '' }} class="w-4 h-4 text-[#1a73e8] border-[var(--border-color)] focus:ring-[#1a73e8]">
                                <span class="text-sm text-[var(--text-main)]">Ready (Tersedia)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="stok" value="po" {{ old('stok', $produk->stok) === 'po' ? 'checked' : '' }} class="w-4 h-4 text-[#1a73e8] border-[var(--border-color)] focus:ring-[#1a73e8]">
                                <span class="text-sm text-[var(--text-main)]">Pre-Order</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="stok" value="habis" {{ old('stok', $produk->stok) === 'habis' ? 'checked' : '' }} class="w-4 h-4 text-[#1a73e8] border-[var(--border-color)] focus:ring-[#1a73e8]">
                                <span class="text-sm text-[var(--text-main)]">Habis</span>
                            </label>
                        </div>
                        @error('stok') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </form>
            </div>
        </div>
        
        <div class="space-y-6">
            <div class="admin-card">
                <div class="admin-card-header bg-[#e8f0fe] border-b-[#1a73e8]/20">
                    <h3 class="text-sm font-bold text-[#1a73e8]">Aksi Perubahan</h3>
                </div>
                <div class="p-5">
                    <p class="text-xs text-[var(--text-muted)] mb-5 leading-relaxed">
                        Perubahan akan diperiksa Admin terlebih dahulu. Informasi produk yang tampil tetap seperti saat ini sampai perubahan disetujui.
                    </p>
                    <button type="button" onclick="document.getElementById('produkForm').submit()" class="w-full btn-primary justify-center py-2.5">
                        Ajukan Perubahan
                    </button>
                    <div class="mt-3 text-center">
                        <a href="{{ route('penjual.produk.index') }}" class="text-xs text-[#1a73e8] hover:underline font-medium">Batal</a>
                    </div>
                </div>
            </div>
            
            <div class="admin-card">
                <div class="admin-card-header bg-[var(--bg-hover)]">
                    <h3 class="text-sm font-bold text-[var(--text-main)]">Performa Produk</h3>
                </div>
                <div class="p-5 text-center">
                    <div class="w-16 h-16 bg-[#e8f0fe] rounded-full mx-auto flex items-center justify-center text-[#1a73e8] mb-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                    <p class="text-sm text-[var(--text-muted)]">Produk ini sudah dilihat</p>
                    <p class="text-3xl font-display font-bold text-[var(--text-main)] my-1">{{ number_format($produk->views) }}</p>
                    <p class="text-xs text-[var(--text-muted)]">kali oleh pengunjung</p>
                    
                    <a href="{{ route('penjual.laporan') }}" class="inline-block mt-4 text-xs font-semibold text-[#1a73e8] hover:underline">
                        Lihat Laporan Toko →
                    </a>
                </div>
            </div>
            
        </div>
    </div>

@endsection

