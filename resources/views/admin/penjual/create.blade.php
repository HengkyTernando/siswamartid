@extends('layouts.admin')

@section('title', 'Tambah Penjual')
@section('page-title', 'Tambah Penjual')

@section('content')

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.penjual.index') }}" class="p-2 rounded-full hover:bg-[var(--bg-hover)] text-[var(--text-muted)] transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h2 class="text-xl font-bold text-[var(--text-main)]">Tambah Akun Penjual</h2>
    </div>

    <div class="admin-card p-6 max-w-3xl">
        <form method="POST" action="{{ route('admin.penjual.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <h3 class="text-sm font-bold text-[var(--text-main)] border-b border-[var(--border-color)] pb-2 mb-4 uppercase tracking-widest">Data Penjual</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="form-input">
                    @error('name') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Email untuk Masuk</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="form-input">
                    @error('email') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Password</label>
                    <input type="password" name="password" required class="form-input" minlength="8">
                    @error('password') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">NISN</label>
                    <input type="text" name="nisn" value="{{ old('nisn') }}" required inputmode="numeric" maxlength="10" class="form-input">
                    @error('nisn') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Foto Penjual</label>
                    <input type="file" name="foto_toko" accept="image/jpeg,image/png,image/webp" class="form-input">
                    @error('foto_toko') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Alamat Penjual</label>
                    <textarea name="alamat" rows="3" required class="form-input">{{ old('alamat') }}</textarea>
                    @error('alamat') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <h3 class="text-sm font-bold text-[var(--text-main)] border-b border-[var(--border-color)] pb-2 mt-8 mb-4 uppercase tracking-widest">Data Toko</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Nama Toko</label>
                    <input type="text" name="nama_toko" value="{{ old('nama_toko') }}" required class="form-input">
                    @error('nama_toko') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Nomor WhatsApp</label>
                    <input type="text" name="nomor_whatsapp" value="{{ old('nomor_whatsapp') }}" required placeholder="Contoh: 08123456789" class="form-input">
                    @error('nomor_whatsapp') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-[var(--text-main)] mb-1.5">Lokasi / Kelas</label>
                    <input type="text" name="lokasi_kelas" value="{{ old('lokasi_kelas') }}" required placeholder="Contoh: Kantin No. 4 atau Kelas XII-A" class="form-input">
                    @error('lokasi_kelas') <p class="text-[#ea4335] text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-4 border-t border-[var(--border-color)] flex justify-end">
                <button type="submit" class="btn-primary">
                    Buat Akun Penjual
                </button>
            </div>
        </form>
    </div>

@endsection
