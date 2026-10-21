@extends('layouts.penjual')

@section('title', 'Tambah Produk Baru')
@section('page-title', 'Tambah Produk Baru')

@section('content')

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('penjual.produk.index') }}" class="p-2 rounded-full hover:bg-[var(--bg-hover)] text-[var(--text-muted)] transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h2 class="text-xl font-bold text-[var(--text-main)]">Tambah Produk</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="admin-card p-6">
                <form method="POST" action="{{ route('penjual.produk.store') }}" enctype="multipart/form-data" class="space-y-6" id="produkForm">
                    @csrf
                    
                    <h3 class="text-sm font-bold text-[var(--text-main)] border-b border-[var(--border-color)] pb-2 mb-4 uppercase tracking-widest">Detail Produk</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Nama Produk</label>
                        <input type="text" name="nama" value="{{ old('nama') }}" required class="form-input">
                        @error('nama') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Kategori</label>
                            <select name="kategori_id" required class="form-input">
                                <option value="" disabled selected>Pilih Kategori</option>
                                @foreach($kategori as $k)
                                    <option value="{{ $k->id }}" {{ old('kategori_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                                @endforeach
                            </select>
                            @error('kategori_id') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Harga (Rp)</label>
                            <input type="number" name="harga" value="{{ old('harga') }}" required min="0" class="form-input">
                            @error('harga') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Deskripsi Produk</label>
                        <textarea name="deskripsi" rows="5" required class="form-input" placeholder="Ceritakan rasa, bahan, varian, atau porsinya...">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <h3 class="text-sm font-bold text-[var(--text-main)] border-b border-[var(--border-color)] pb-2 mt-8 mb-4 uppercase tracking-widest">Foto & Ketersediaan</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Foto Produk <span class="text-xs text-[var(--text-muted)] font-normal">(Opsional)</span></label>
                        <input type="file" name="foto_utama" accept="image/*" class="w-full text-sm text-[var(--text-muted)] file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[var(--bg-active)] file:text-[#1a73e8] hover:file:bg-[#d2e3fc] transition cursor-pointer">
                        <p class="text-xs text-[var(--text-muted)] mt-1">JPG, PNG, atau WEBP. Maksimal 2MB.</p>
                        @error('foto_utama') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Status Ketersediaan</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="stok" value="tersedia" {{ old('stok', 'tersedia') === 'tersedia' ? 'checked' : '' }} class="w-4 h-4 text-[#1a73e8] border-[var(--border-color)] focus:ring-[#1a73e8]">
                                <span class="text-sm text-[var(--text-main)]">Ready (Tersedia)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="stok" value="po" {{ old('stok') === 'po' ? 'checked' : '' }} class="w-4 h-4 text-[#1a73e8] border-[var(--border-color)] focus:ring-[#1a73e8]">
                                <span class="text-sm text-[var(--text-main)]">Pre-Order</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="stok" value="habis" {{ old('stok') === 'habis' ? 'checked' : '' }} class="w-4 h-4 text-[#1a73e8] border-[var(--border-color)] focus:ring-[#1a73e8]">
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
                    <h3 class="text-sm font-bold text-[#1a73e8]">Publikasi</h3>
                </div>
                <div class="p-5">
                    <p class="text-xs text-[var(--text-muted)] mb-5 leading-relaxed">Produkmu akan masuk status <span class="font-bold">Pending (Menunggu Moderasi)</span>. Admin akan mengeceknya sebelum produk tampil di katalog.</p>
                    <button type="button" onclick="document.getElementById('produkForm').submit()" class="w-full btn-primary justify-center py-2.5">
                        Kirim Produk
                    </button>
                    <div class="mt-3 text-center">
                        <a href="{{ route('penjual.produk.index') }}" class="text-xs text-[#1a73e8] hover:underline font-medium">Batal</a>
                    </div>
                </div>
            </div>
            
            <div class="bg-[#fef7e0] border border-[#fbbc04] rounded-2xl p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-[#b06000] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <p class="text-sm font-semibold text-[#b06000]">Catatan</p>
                        <p class="text-xs text-[#b06000]/80 mt-0.5">Transaksi dilakukan langsung dengan pembeli. Pastikan nomor WhatsApp di profilmu sudah benar.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
