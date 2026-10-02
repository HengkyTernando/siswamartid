@extends('layouts.admin')

@section('title', 'Detail Penjual')
@section('page-title', 'Detail Penjual')

@section('content')
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.penjual.index') }}" class="p-2 rounded-full hover:bg-[var(--bg-hover)] text-[var(--text-muted)]">Kembali</a>
        <h2 class="text-xl font-bold text-[var(--text-main)]">Detail {{ $penjual->penjual?->nama_toko ?? $penjual->name }}</h2>
    </div>

    <div class="admin-card p-6 max-w-3xl space-y-6">
        <div class="flex items-center gap-4">
            @if($penjual->penjual?->foto_toko)
                <img src="{{ asset('storage/' . $penjual->penjual->foto_toko) }}" alt="Foto {{ $penjual->name }}" class="w-20 h-20 rounded-xl object-cover border border-[var(--border-color)]">
            @else
                <div class="w-20 h-20 rounded-xl bg-[var(--bg-hover)] flex items-center justify-center font-bold text-2xl">{{ strtoupper(substr($penjual->name, 0, 1)) }}</div>
            @endif
            <div>
                <h3 class="text-xl font-bold text-[var(--text-main)]">{{ $penjual->name }}</h3>
                <p class="text-sm text-[var(--text-muted)]">{{ $penjual->email }}</p>
            </div>
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
            <div><dt class="text-[var(--text-muted)]">NISN</dt><dd class="font-semibold">{{ $penjual->penjual?->nisn ?? '-' }}</dd></div>
            <div><dt class="text-[var(--text-muted)]">Nama Toko</dt><dd class="font-semibold">{{ $penjual->penjual?->nama_toko ?? '-' }}</dd></div>
            <div><dt class="text-[var(--text-muted)]">Nomor WhatsApp</dt><dd class="font-semibold">{{ $penjual->penjual?->nomor_whatsapp ?? '-' }}</dd></div>
            <div><dt class="text-[var(--text-muted)]">Lokasi / Kelas</dt><dd class="font-semibold">{{ $penjual->penjual?->lokasi_kelas ?? '-' }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-[var(--text-muted)]">Alamat</dt><dd class="font-semibold whitespace-pre-line">{{ $penjual->penjual?->alamat ?? '-' }}</dd></div>
            <div><dt class="text-[var(--text-muted)]">Login Terakhir</dt><dd class="font-semibold">{{ $penjual->penjual?->last_login_at?->format('d M Y, H:i:s') ?? 'Belum ada' }}</dd></div>
            <div><dt class="text-[var(--text-muted)]">Aktivitas Terakhir</dt><dd class="font-semibold">{{ $penjual->penjual?->last_activity_at?->format('d M Y, H:i:s') ?? 'Belum ada' }}</dd></div>
            <div><dt class="text-[var(--text-muted)]">Jenis Aktivitas</dt><dd class="font-semibold">{{ $penjual->penjual?->last_activity_type ?? '-' }}</dd></div>
        </dl>

        <a href="{{ route('admin.penjual.edit', $penjual) }}" class="btn-primary inline-flex">Edit Data Penjual</a>
    </div>
@endsection
