@extends('layouts.admin')

@section('title', 'Moderasi Produk')
@section('page-title', 'Moderasi Produk')

@section('content')

    <div class="mb-6">
        <h2 class="text-xl font-bold text-[var(--text-main)]">Moderasi Produk</h2>
        <p class="text-sm text-[var(--text-muted)] mt-1">Cek dan setujui produk sebelum tampil di katalog.</p>
    </div>

    {{-- Filter Tabs --}}
    <div class="flex gap-2 mb-6 border-b border-[var(--border-color)] overflow-x-auto pb-2">
        <a href="{{ route('admin.produk.index') }}" class="px-4 py-2 text-sm rounded-t-lg transition-colors {{ request('status') === null ? 'text-[var(--text-main)] font-black border-b-4 border-[var(--color-primary)] bg-[var(--color-primary-light)]' : 'text-[var(--text-muted)] font-bold hover:bg-[var(--bg-surface)] hover:text-[var(--text-main)] border-b-4 border-transparent' }}">Semua</a>
        <a href="{{ route('admin.produk.index', ['status' => 'pending']) }}" class="px-4 py-2 text-sm rounded-t-lg transition-colors {{ request('status') === 'pending' ? 'text-[var(--text-main)] font-black border-b-4 border-[var(--color-primary)] bg-[var(--color-primary-light)]' : 'text-[var(--text-muted)] font-bold hover:bg-[var(--bg-surface)] hover:text-[var(--text-main)] border-b-4 border-transparent' }}">Menunggu Moderasi <span class="bg-[var(--color-danger)] text-white border border-[#241A14] font-bold text-[10px] px-1.5 py-0.5 rounded-full ml-1">{{ \App\Models\Produk::where('status', 'pending')->count() }}</span></a>
        <a href="{{ route('admin.produk.index', ['status' => 'aktif']) }}" class="px-4 py-2 text-sm rounded-t-lg transition-colors {{ request('status') === 'aktif' ? 'text-[var(--text-main)] font-black border-b-4 border-[var(--color-primary)] bg-[var(--color-primary-light)]' : 'text-[var(--text-muted)] font-bold hover:bg-[var(--bg-surface)] hover:text-[var(--text-main)] border-b-4 border-transparent' }}">Aktif</a>
        <a href="{{ route('admin.produk.index', ['status' => 'ditolak']) }}" class="px-4 py-2 text-sm rounded-t-lg transition-colors {{ request('status') === 'ditolak' ? 'text-[var(--text-main)] font-black border-b-4 border-[var(--color-primary)] bg-[var(--color-primary-light)]' : 'text-[var(--text-muted)] font-bold hover:bg-[var(--bg-surface)] hover:text-[var(--text-main)] border-b-4 border-transparent' }}">Ditolak</a>
    </div>

    <div class="admin-card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-[var(--bg-hover)] border-b border-[var(--border-color)]">
                    <tr>
                        <th class="px-5 py-4 text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider">Info Produk</th>
                        <th class="px-5 py-4 text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider">Penjual</th>
                        <th class="px-5 py-4 text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider">Harga</th>
                        <th class="px-5 py-4 text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider">Status</th>
                        <th class="px-5 py-4 text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border-color)]">
                    @forelse($produk as $p)
                    <tr class="hover:bg-[var(--bg-hover)] transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-4">
                                @if($p->foto_utama)
                                    <img src="{{ asset('storage/' . $p->foto_utama) }}" class="w-12 h-12 rounded-lg object-cover border border-[var(--border-color)]" alt="{{ $p->nama }}">
                                @else
                                    <div class="w-12 h-12 rounded-lg bg-[var(--bg-hover)] border border-[var(--border-color)] flex items-center justify-center text-[var(--text-muted)]">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                                <div>
                                    <div class="font-semibold text-[var(--text-main)]">{{ $p->nama }}</div>
                                    <div class="text-[10px] bg-[var(--color-primary)] text-[var(--border-color)] border border-[var(--border-color)] shadow-[1px_1px_0px_0px_var(--border-color)] font-bold px-2 py-0.5 rounded-full inline-block mt-1">{{ $p->kategori->nama }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="text-[var(--text-main)] font-medium text-sm">{{ $p->penjual->nama_toko }}</div>
                            <div class="text-xs text-[var(--text-muted)]">{{ $p->penjual->lokasi_kelas }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-medium text-[var(--text-main)]">Rp {{ number_format($p->harga, 0, ',', '.') }}</div>
                            <div class="text-xs text-[var(--text-muted)] mt-0.5">Stok: {{ ucfirst($p->stok) }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="badge badge-{{ $p->status === 'aktif' ? 'aktif' : ($p->status === 'pending' ? 'pending' : ($p->status === 'ditolak' ? 'ditolak' : 'draft')) }}">
                                {{ ucfirst($p->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.produk.show', $p) }}" class="btn-outline text-xs px-3 py-1.5">Review</a>
                                
                                @if($p->status === 'pending')
                                <form method="POST" action="{{ route('admin.produk.updateStatus', $p) }}" class="inline-block">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="aktif">
                                    <button type="submit" class="btn-primary text-xs px-3 py-1.5" style="background:#34a853">Setujui</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-[var(--text-muted)]">
                            Belum ada produk yang perlu dicek.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-[var(--border-color)]">
            {{ $produk->links() }}
        </div>
    </div>

@endsection