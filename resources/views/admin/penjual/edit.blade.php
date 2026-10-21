@extends('layouts.admin')

@section('title', 'Edit Penjual')
@section('page-title', 'Edit Penjual')

@section('content')

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.penjual.index') }}" class="p-2 rounded-full hover:bg-[var(--bg-hover)] text-[var(--text-muted)] transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h2 class="text-xl font-bold text-[var(--text-main)]">Edit Data Penjual</h2>
    </div>

    <div class="admin-card p-6 max-w-3xl">
        <form method="POST" action="{{ route('admin.penjual.update', $penjual) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf @method('PUT')

            <h3 class="text-sm font-bold text-[var(--text-main)] border-b border-[var(--border-color)] pb-2 mb-4 uppercase tracking-widest">Data Penjual</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $penjual->name) }}" required class="form-input">
                    @error('name') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Email untuk Masuk</label>
                    <input type="email" name="email" value="{{ old('email', $penjual->email) }}" required class="form-input">
                    @error('email') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Password <span class="text-xs font-normal text-[var(--text-muted)]">(Kosongkan jika tidak diubah)</span></label>
                    <input type="password" name="password" class="form-input" minlength="8">
                    @error('password') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">NISN</label>
                    <input type="text" name="nisn" value="{{ old('nisn', $penjual->penjual?->nisn) }}" required inputmode="numeric" maxlength="10" class="form-input">
                    @error('nisn') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Foto Penjual</label>
                    <input type="file" name="foto_toko" accept="image/jpeg,image/png,image/webp" class="form-input">
                    @error('foto_toko') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                    @if($penjual->penjual?->foto_toko)
                        <img src="{{ asset('storage/' . $penjual->penjual->foto_toko) }}" alt="Foto penjual" class="mt-2 w-16 h-16 rounded-lg object-cover">
                    @endif
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Alamat Penjual</label>
                    <textarea name="alamat" rows="3" required class="form-input">{{ old('alamat', $penjual->penjual?->alamat) }}</textarea>
                    @error('alamat') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <h3 class="text-sm font-bold text-[var(--text-main)] border-b border-[var(--border-color)] pb-2 mt-8 mb-4 uppercase tracking-widest">Data Toko</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Nama Toko</label>
                    <input type="text" name="nama_toko" value="{{ old('nama_toko', $penjual->penjual?->nama_toko) }}" required class="form-input">
                    @error('nama_toko') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Nomor WhatsApp</label>
                    <input type="text" name="nomor_whatsapp" value="{{ old('nomor_whatsapp', $penjual->penjual?->nomor_whatsapp) }}" required class="form-input">
                    @error('nomor_whatsapp') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Lokasi / Kelas</label>
                    <input type="text" name="lokasi_kelas" value="{{ old('lokasi_kelas', $penjual->penjual?->lokasi_kelas) }}" required class="form-input">
                    @error('lokasi_kelas') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-4 border-t border-[var(--border-color)] flex justify-end">
                <button type="submit" class="btn-primary">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

@endsection
