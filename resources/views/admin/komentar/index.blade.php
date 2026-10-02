@extends('layouts.admin')

@section('title', 'Moderasi Komentar & Filter Kata')
@section('page-title', 'Moderasi Komentar')

@section('content')
<div class="space-y-6" x-data="{ 
    activeTab: '{{ $status === 'filter-kata' || $tab === 'filter-kata' ? 'filter-kata' : ($tab === 'whitelist' ? 'whitelist' : 'komentar') }}',
    currentFilterStatus: '{{ $status }}',
    
    // Modal state
    detailModalOpen: false,
    censorModalOpen: false,
    selectedKomentar: null,
    
    // Action modal
    deleteModalOpen: false,
    deleteUrl: '',
    
    // Edit BadWord Modal
    editBadWordModalOpen: false,
    editBadWordWord: '',
    editBadWordLanguage: 'indonesian',
    editBadWordSeverity: 'medium',
    editBadWordCategory: '',
    editBadWordUrl: '',

    openSensorPreview(komentar) {
        this.selectedKomentar = komentar;
        this.censorModalOpen = true;
    }
}">

    {{-- ─── Header Dashboard Moderasi ───────────────────────────────── --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-neutral-900 uppercase tracking-tight flex items-center gap-2">
                <span>🛡️</span> Moderasi Komentar SiswaMart
            </h2>
            <p class="text-sm font-bold text-neutral-500">
                Kelola komentar, sensor kata sensitif, dan daftar kata yang perlu difilter.
            </p>
        </div>

        {{-- Sub Navigation Tabs --}}
        <div class="flex items-center gap-1.5 bg-[#FFF8E8] border-2 border-neutral-900 p-1.5 rounded-2xl shadow-[3px_3px_0px_0px_#000000]">
            <a href="{{ route('admin.komentar.index', ['status' => 'filtered', 'tab' => 'komentar']) }}"
               class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all {{ ($tab === 'komentar' || !$tab) && ($status === 'filtered' || $status === 'perlu-moderasi') ? 'bg-[#FF5722] text-white shadow-[2px_2px_0px_0px_#000000]' : 'text-neutral-700 hover:bg-white/80' }}">
                ⚠️ Perlu Moderasi ({{ number_format($stats['perlu_moderasi']) }})
            </a>
            <a href="{{ route('admin.komentar.index', ['status' => 'semua', 'tab' => 'komentar']) }}"
               class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all {{ ($tab === 'komentar' || !$tab) && $status !== 'filtered' && $status !== 'perlu-moderasi' ? 'bg-[#FF5722] text-white shadow-[2px_2px_0px_0px_#000000]' : 'text-neutral-700 hover:bg-white/80' }}">
                💬 Semua Komentar
            </a>
            <a href="{{ route('admin.komentar.index', ['tab' => 'filter-kata']) }}"
               class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all {{ $tab === 'filter-kata' ? 'bg-[#FF5722] text-white shadow-[2px_2px_0px_0px_#000000]' : 'text-neutral-700 hover:bg-white/80' }}">
                ⚙️ Daftar Kata ({{ count($badWords) }})
            </a>
            <a href="{{ route('admin.komentar.index', ['tab' => 'whitelist']) }}"
               class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all {{ $tab === 'whitelist' ? 'bg-[#FF5722] text-white shadow-[2px_2px_0px_0px_#000000]' : 'text-neutral-700 hover:bg-white/80' }}">
                ✅ Kata Aman ({{ count($whitelists) }})
            </a>
        </div>
    </div>

    {{-- ─── REAL STATISTICS CARDS ────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Total Komentar --}}
        <div class="bg-white p-5 rounded-3xl border-2 border-neutral-900 shadow-[4px_4px_0px_0px_#000000] flex items-center justify-between">
            <div>
                <p class="text-xs font-black text-neutral-500 uppercase tracking-widest">Total Komentar</p>
                <p class="text-3xl font-black text-neutral-900 mt-1">{{ number_format($stats['total']) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-100 border-2 border-neutral-900 flex items-center justify-center text-xl font-black shadow-[2px_2px_0px_0px_#000000]">
                💬
            </div>
        </div>

        {{-- Card 2: Perlu Moderasi (Filtered) --}}
        <div class="bg-[#FFF3E0] p-5 rounded-3xl border-2 border-neutral-900 shadow-[4px_4px_0px_0px_#000000] flex items-center justify-between">
            <div>
                <p class="text-xs font-black text-orange-800 uppercase tracking-widest">Perlu Moderasi</p>
                <p class="text-3xl font-black text-[#E65100] mt-1">{{ number_format($stats['perlu_moderasi']) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-orange-400 border-2 border-neutral-900 flex items-center justify-center text-xl font-black text-white shadow-[2px_2px_0px_0px_#000000]">
                ⚠️
            </div>
        </div>

        {{-- Card 3: Ditampilkan (Approved) --}}
        <div class="bg-[#E8F5E9] p-5 rounded-3xl border-2 border-neutral-900 shadow-[4px_4px_0px_0px_#000000] flex items-center justify-between">
            <div>
                <p class="text-xs font-black text-emerald-800 uppercase tracking-widest">Ditampilkan</p>
                <p class="text-3xl font-black text-[#2E7D32] mt-1">{{ number_format($stats['approved']) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-400 border-2 border-neutral-900 flex items-center justify-center text-xl font-black text-white shadow-[2px_2px_0px_0px_#000000]">
                ✓
            </div>
        </div>

        {{-- Card 4: Ditolak (Rejected) --}}
        <div class="bg-[#FFEBEE] p-5 rounded-3xl border-2 border-neutral-900 shadow-[4px_4px_0px_0px_#000000] flex items-center justify-between">
            <div>
                <p class="text-xs font-black text-rose-800 uppercase tracking-widest">Ditolak</p>
                <p class="text-3xl font-black text-[#C62828] mt-1">{{ number_format($stats['rejected']) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-400 border-2 border-neutral-900 flex items-center justify-center text-xl font-black text-white shadow-[2px_2px_0px_0px_#000000]">
                ✕
            </div>
        </div>
    </div>

    {{-- ─── FLASH MESSAGES ───────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="bg-[#DCFCE7] border-2 border-[#16A34A] text-[#15803D] font-black px-5 py-3.5 rounded-2xl shadow-[3px_3px_0px_0px_#16A34A] flex items-center justify-between">
            <span>🎉 {{ session('success') }}</span>
        </div>
    @endif

    {{-- ─── TAB 1: MODERASI & ANTREAN KOMENTAR ───────────────────────── --}}
    <div x-show="activeTab === 'komentar'" class="space-y-6">

        {{-- Status Filter Pills --}}
        <div class="flex flex-wrap items-center justify-between gap-4 bg-white p-4 rounded-3xl border-2 border-neutral-900 shadow-[4px_4px_0px_0px_#000000]">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.komentar.index', ['status' => 'semua', 'q' => $search, 'rating' => $rating, 'sort' => $sort]) }}"
                   class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider border-2 border-neutral-900 transition-all {{ $status === 'semua' ? 'bg-neutral-900 text-white shadow-[2px_2px_0px_0px_#000000]' : 'bg-neutral-100 text-neutral-800 hover:bg-neutral-200' }}">
                    Semua ({{ number_format($stats['total']) }})
                </a>
                <a href="{{ route('admin.komentar.index', ['status' => 'filtered', 'q' => $search, 'rating' => $rating, 'sort' => $sort]) }}"
                   class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider border-2 border-neutral-900 transition-all {{ $status === 'filtered' || $status === 'perlu-moderasi' ? 'bg-[#FF5722] text-white shadow-[2px_2px_0px_0px_#000000]' : 'bg-[#FFF3E0] text-[#E65100] hover:bg-[#FFE0B2]' }}">
                    Perlu Moderasi ({{ number_format($stats['perlu_moderasi']) }})
                </a>
                <a href="{{ route('admin.komentar.index', ['status' => 'approved', 'q' => $search, 'rating' => $rating, 'sort' => $sort]) }}"
                   class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider border-2 border-neutral-900 transition-all {{ $status === 'approved' || $status === 'tampil' ? 'bg-[#2E7D32] text-white shadow-[2px_2px_0px_0px_#000000]' : 'bg-[#E8F5E9] text-[#2E7D32] hover:bg-[#C8E6C9]' }}">
                    Tampil ({{ number_format($stats['approved']) }})
                </a>
                <a href="{{ route('admin.komentar.index', ['status' => 'rejected', 'q' => $search, 'rating' => $rating, 'sort' => $sort]) }}"
                   class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider border-2 border-neutral-900 transition-all {{ $status === 'rejected' || $status === 'ditolak' ? 'bg-[#C62828] text-white shadow-[2px_2px_0px_0px_#000000]' : 'bg-[#FFEBEE] text-[#C62828] hover:bg-[#FFCDD2]' }}">
                    Ditolak ({{ number_format($stats['rejected']) }})
                </a>
            </div>

            {{-- Form Pencarian & Sort --}}
            <form method="GET" action="{{ route('admin.komentar.index') }}" class="flex items-center gap-2">
                <input type="hidden" name="status" value="{{ $status }}">
                <input type="hidden" name="tab" value="komentar">

                <div class="relative">
                    <input type="text"
                           name="q"
                           value="{{ $search }}"
                            placeholder="Cari nama atau komentar..."
                           class="w-48 lg:w-64 bg-neutral-50 border-2 border-neutral-900 rounded-xl px-3.5 py-1.5 text-xs font-bold focus:outline-none focus:bg-white">
                </div>

                <select name="sort" onchange="this.form.submit()" class="bg-neutral-50 border-2 border-neutral-900 rounded-xl px-3 py-1.5 text-xs font-bold">
                    <option value="terbaru" {{ $sort == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="terlama" {{ $sort == 'terlama' ? 'selected' : '' }}>Terlama</option>
                </select>
            </form>
        </div>

        {{-- ── SEKSI UTAMA: KOMENTAR PERLU MODERASI / LIST KOMENTAR ── --}}
        @if($status === 'filtered' || $status === 'perlu-moderasi')
            <div class="bg-[#FFF8E8] border-3 border-neutral-900 p-6 rounded-3xl shadow-[5px_5px_0px_0px_#000000] space-y-4">
                <div class="flex items-center justify-between border-b-2 border-neutral-900 pb-4">
                    <div>
                        <h3 class="text-xl font-black text-neutral-900 uppercase tracking-tight flex items-center gap-2">
                            <span>⚠️</span> Komentar Perlu Moderasi
                        </h3>
                        <p class="text-xs font-bold text-neutral-600 mt-0.5">
                            Komentar yang terdeteksi mengandung kata sensitif dan perlu dicek.
                        </p>
                    </div>
                    <span class="bg-[#FF5722] text-white text-xs font-black px-3.5 py-1.5 rounded-full border-2 border-neutral-900 shadow-[2px_2px_0px_0px_#000000]">
                        {{ number_format($komentarList->total()) }} Menunggu
                    </span>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    @forelse($komentarList as $row)
                        @php
                            $detectedData = is_array($row->detected_words) ? $row->detected_words : (json_decode($row->detected_words, true) ?? []);
                            $wordsList = $detectedData['words'] ?? [];
                            $severity = ucfirst($detectedData['severity'] ?? 'medium');
                            $wordCount = count($wordsList);
                            
                            // Auto generate censored text preview
                            $modService = app(\App\Services\CommentModerationService::class);
                            $autoCensored = $modService->moderate($row->komentar)['censored_text'];
                        @endphp
                        
                        <div class="bg-white p-5 rounded-2xl border-2 border-neutral-900 shadow-[4px_4px_0px_0px_#000000] flex flex-col justify-between gap-4">
                            {{-- Header Card --}}
                            <div class="flex flex-wrap items-start justify-between gap-2 border-b border-neutral-200 pb-3">
                                <div>
                                    <div class="text-xs font-black text-neutral-500 uppercase">
                                        Produk: <span class="text-neutral-900 font-extrabold">{{ $row->produk->nama ?? 'Produk Dihapus' }}</span>
                                    </div>
                                    <div class="text-sm font-black text-neutral-900 flex items-center gap-2 mt-0.5">
                                        <span class="w-6 h-6 rounded-full bg-[#FFD84D] border border-neutral-900 text-center font-black text-xs leading-5">
                                            {{ strtoupper(substr($row->guest_username, 0, 1)) }}
                                        </span>
                                        <span>User: {{ $row->guest_username }}</span>
                                        <span class="text-yellow-500 text-xs tracking-tighter">
                                            @for($i=1; $i<=5; $i++)
                                                {{ $i <= $row->rating ? '★' : '☆' }}
                                            @endfor
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[11px] font-bold text-neutral-400 uppercase">
                                        {{ $row->created_at->format('d F Y · H:i') }}
                                    </div>
                                </div>
                            </div>

                            {{-- Isi Komentar --}}
                            <div class="bg-neutral-50 p-4 rounded-xl border border-neutral-300 font-bold text-neutral-800 leading-relaxed text-sm">
                                "{{ $row->komentar }}"
                            </div>

                            {{-- Info Deteksi Badword --}}
                            <div class="bg-amber-50 border border-amber-300 p-3 rounded-xl flex items-center justify-between text-xs font-bold text-amber-900">
                                <div class="flex items-center gap-2">
                                    <span class="text-base">⚠️</span>
                                    <div>
                                        <span class="font-extrabold uppercase">Terdeteksi:</span>
                                        <span>{{ $wordCount }} kata terdeteksi ({{ implode(', ', $wordsList) }})</span>
                                    </div>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $severity === 'High' ? 'bg-red-200 text-red-900' : 'bg-amber-200 text-amber-900' }}">
                                    Severity {{ $severity }}
                                </span>
                            </div>

                            {{-- Tombol Aksi Admin --}}
                            <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                                <button type="button"
                                        @click="selectedKomentar = {{ json_encode([
                                            'id' => $row->id,
                                            'user' => $row->guest_username,
                                            'produk' => $row->produk->nama ?? 'Produk Dihapus',
                                            'rating' => $row->rating,
                                            'komentar' => $row->komentar,
                                            'auto_censored' => $autoCensored,
                                            'display_content' => $row->display_content,
                                            'detected_words' => implode(', ', $wordsList),
                                            'severity' => $severity,
                                            'tanggal' => $row->created_at->format('d M Y H:i'),
                                            'status' => $row->moderation_status ?? $row->status,
                                        ]) }}; detailModalOpen = true"
                                        class="px-3.5 py-1.5 rounded-xl text-xs font-black uppercase tracking-wider bg-neutral-100 hover:bg-neutral-200 border border-neutral-900">
                                    🔍 Detail Komentar
                                </button>

                                <div class="flex items-center gap-2">
                                    {{-- A. TAMPILKAN --}}
                                    <form method="POST" action="{{ route('admin.komentar.approve', $row->id) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider bg-[#2E7D32] hover:bg-[#1B5E20] text-white border-2 border-neutral-900 shadow-[2px_2px_0px_0px_#000000] transition-all">
                                            ✓ Tampilkan
                                        </button>
                                    </form>

                                    {{-- B. SENSOR (Modal Trigger) --}}
                                    <button type="button"
                                            @click="openSensorPreview({{ json_encode([
                                                'id' => $row->id,
                                                'komentar' => $row->komentar,
                                                'auto_censored' => $autoCensored,
                                                'user' => $row->guest_username,
                                                'produk' => $row->produk->nama ?? 'Produk Dihapus',
                                            ]) }})"
                                            class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider bg-[#FF9800] hover:bg-[#E65100] text-white border-2 border-neutral-900 shadow-[2px_2px_0px_0px_#000000] transition-all">
                                        ✦ Sensor
                                    </button>

                                    {{-- C. TOLAK --}}
                                    <form method="POST" action="{{ route('admin.komentar.reject', $row->id) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                onclick="return confirm('Tolak komentar ini? Komentar tetap ada di database namun tidak tampil publik.')"
                                                class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider bg-[#C62828] hover:bg-[#B71C1C] text-white border-2 border-neutral-900 shadow-[2px_2px_0px_0px_#000000] transition-all">
                                            ✕ Tolak
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white p-12 text-center rounded-2xl border-2 border-neutral-900">
                            <span class="text-4xl block mb-2">🎉</span>
                            <p class="text-base font-black text-neutral-800">Tidak ada komentar yang memerlukan moderasi saat ini.</p>
                            <p class="text-xs font-bold text-neutral-500 mt-1">Semua komentar yang dikirim pengguna berada dalam kondisi bersih atau telah selesai dimoderasi.</p>
                        </div>
                    @endforelse
                </div>

                @if($komentarList->hasPages())
                    <div class="pt-4 border-t-2 border-neutral-900">
                        {{ $komentarList->links() }}
                    </div>
                @endif
            </div>

        {{-- ── SEKSI UMUM: DAFTAR SEMUA / TAMPIL / DITOLAK KOMENTAR ── --}}
        @else
            <div class="bg-white rounded-3xl border-2 border-neutral-900 shadow-[4px_4px_0px_0px_#000000] overflow-hidden">
                <div class="p-4 bg-neutral-100 border-b-2 border-neutral-900 flex items-center justify-between">
                    <h3 class="font-black text-sm uppercase text-neutral-900">
                        Daftar Komentar (Status: {{ strtoupper($status) }})
                    </h3>
                    <span class="text-xs font-bold text-neutral-600">Total {{ number_format($komentarList->total()) }} record</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b-2 border-neutral-900 bg-neutral-50 text-xs font-black uppercase text-neutral-600 tracking-wider">
                                <th class="py-3.5 px-4">User</th>
                                <th class="py-3.5 px-4">Produk</th>
                                <th class="py-3.5 px-4 text-center">Rating</th>
                                <th class="py-3.5 px-4">Komentar Tampil</th>
                                <th class="py-3.5 px-4">Tanggal</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                                <th class="py-3.5 px-4 text-right">Aksi Admin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 text-sm font-semibold">
                            @forelse($komentarList as $row)
                                @php
                                    $modStatus = $row->moderation_status ?? $row->status;
                                    $modService = app(\App\Services\CommentModerationService::class);
                                    $autoCensored = $modService->moderate($row->komentar)['censored_text'];
                                @endphp
                                <tr class="hover:bg-neutral-50 transition-colors">
                                    <td class="py-3.5 px-4 font-bold text-neutral-900 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-[#FFD84D] border border-neutral-900 flex items-center justify-center font-black text-xs">
                                                {{ strtoupper(substr($row->guest_username, 0, 1)) }}
                                            </div>
                                            <span>{{ $row->guest_username }}</span>
                                        </div>
                                    </td>

                                    <td class="py-3.5 px-4 max-w-[180px] truncate">
                                        @if($row->produk)
                                            <a href="{{ route('public.produk.show', $row->produk->slug) }}" target="_blank" class="text-[#FF5722] hover:underline font-extrabold">
                                                {{ $row->produk->nama }}
                                            </a>
                                        @else
                                            <span class="text-neutral-400 italic">Produk dihapus</span>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="text-yellow-500 font-bold">
                                            @for($i=1; $i<=5; $i++)
                                                {{ $i <= $row->rating ? '★' : '☆' }}
                                            @endfor
                                        </span>
                                    </td>

                                    <td class="py-3.5 px-4 max-w-[280px]">
                                        <div class="truncate text-neutral-800 font-bold" title="{{ $row->public_content }}">
                                            "{{ $row->public_content }}"
                                        </div>
                                        @if($row->display_content)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-black text-amber-800 bg-amber-100 border border-amber-300 px-2 py-0.5 rounded-full mt-1">
                                                ✦ Versi Disensor Admin
                                            </span>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-4 whitespace-nowrap text-xs text-neutral-500 font-medium">
                                        {{ $row->created_at->format('d M Y') }}<br>
                                        <span class="text-[10px] text-neutral-400">{{ $row->created_at->format('H:i') }}</span>
                                    </td>

                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if($modStatus === 'approved' || $modStatus === 'aktif')
                                            <span class="px-3 py-1 rounded-full text-xs font-black bg-[#E8F5E9] text-[#2E7D32] border border-[#2E7D32]">
                                                Approved
                                            </span>
                                        @elseif($modStatus === 'filtered')
                                            <span class="px-3 py-1 rounded-full text-xs font-black bg-[#FFF3E0] text-[#E65100] border border-[#E65100]">
                                                Filtered
                                            </span>
                                        @elseif($modStatus === 'rejected')
                                            <span class="px-3 py-1 rounded-full text-xs font-black bg-[#FFEBEE] text-[#C62828] border border-[#C62828]">
                                                Rejected
                                            </span>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-xs font-black bg-neutral-100 text-neutral-700 border border-neutral-300">
                                                {{ ucfirst($modStatus) }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button"
                                                    @click="selectedKomentar = {{ json_encode([
                                                        'id' => $row->id,
                                                        'user' => $row->guest_username,
                                                        'produk' => $row->produk->nama ?? 'Produk Dihapus',
                                                        'rating' => $row->rating,
                                                        'komentar' => $row->komentar,
                                                        'auto_censored' => $autoCensored,
                                                        'display_content' => $row->display_content,
                                                        'detected_words' => is_array($row->detected_words) ? implode(', ', $row->detected_words['words'] ?? []) : '',
                                                        'severity' => is_array($row->detected_words) ? ($row->detected_words['severity'] ?? 'medium') : 'none',
                                                        'tanggal' => $row->created_at->format('d M Y H:i'),
                                                        'status' => $modStatus,
                                                    ]) }}; detailModalOpen = true"
                                                    class="px-2.5 py-1 text-xs font-bold text-blue-700 bg-blue-50 border border-blue-300 rounded-lg hover:bg-blue-100">
                                                Detail
                                            </button>

                                            @if($modStatus !== 'approved')
                                                <form method="POST" action="{{ route('admin.komentar.approve', $row->id) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="px-2.5 py-1 text-xs font-bold text-green-700 bg-green-50 border border-green-300 rounded-lg hover:bg-green-100">
                                                        Approve
                                                    </button>
                                                </form>
                                            @endif

                                            <button type="button"
                                                    @click="openSensorPreview({{ json_encode([
                                                        'id' => $row->id,
                                                        'komentar' => $row->komentar,
                                                        'auto_censored' => $autoCensored,
                                                        'user' => $row->guest_username,
                                                        'produk' => $row->produk->nama ?? 'Produk Dihapus',
                                                    ]) }})"
                                                    class="px-2.5 py-1 text-xs font-bold text-amber-700 bg-amber-50 border border-amber-300 rounded-lg hover:bg-amber-100">
                                                Sensor
                                            </button>

                                            @if($modStatus !== 'rejected')
                                                <form method="POST" action="{{ route('admin.komentar.reject', $row->id) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="px-2.5 py-1 text-xs font-bold text-red-700 bg-red-50 border border-red-300 rounded-lg hover:bg-red-100">
                                                        Reject
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-neutral-500 font-bold">
                                        Tidak ada komentar ditemukan untuk filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($komentarList->hasPages())
                    <div class="p-4 border-t-2 border-neutral-900">
                        {{ $komentarList->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    {{-- ─── TAB 2: MANAGEMENT FILTER KATA (BAD WORDS) ────────────────── --}}
    <div x-show="activeTab === 'filter-kata'" class="space-y-6">
        {{-- Form Tambah Custom Kata --}}
        <div class="bg-white p-6 rounded-3xl border-2 border-neutral-900 shadow-[4px_4px_0px_0px_#000000]">
            <h3 class="text-lg font-black uppercase text-neutral-900 mb-4 flex items-center gap-2">
                <span>➕</span> Tambah Kata Terlarang (Custom Badword)
            </h3>
            <form method="POST" action="{{ route('admin.komentar.badWords.store') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3">
                @csrf
                <div class="md:col-span-4">
                    <label class="block text-xs font-black uppercase text-neutral-600 mb-1">Kata / Frasa</label>
                    <input type="text" name="word" required placeholder="misal: anjing, asu..." class="w-full bg-neutral-50 border-2 border-neutral-900 rounded-xl px-3.5 py-2 text-xs font-bold">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-black uppercase text-neutral-600 mb-1">Bahasa</label>
                    <select name="language" class="w-full bg-neutral-50 border-2 border-neutral-900 rounded-xl px-3 py-2 text-xs font-bold">
                        <option value="indonesian">Indonesia</option>
                        <option value="english">English</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-black uppercase text-neutral-600 mb-1">Severity</label>
                    <select name="severity" class="w-full bg-neutral-50 border-2 border-neutral-900 rounded-xl px-3 py-2 text-xs font-bold">
                        <option value="low">Ringan (Low)</option>
                        <option value="medium" selected>Normal (Medium)</option>
                        <option value="high">Ketat (High)</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-black uppercase text-neutral-600 mb-1">Kategori</label>
                    <input type="text" name="category" placeholder="hinaan / makian" class="w-full bg-neutral-50 border-2 border-neutral-900 rounded-xl px-3.5 py-2 text-xs font-bold">
                </div>
                <div class="md:col-span-2 flex items-end">
                    <button type="submit" class="w-full bg-[#FF5722] hover:bg-[#E65100] text-white font-black text-xs uppercase px-4 py-2.5 rounded-xl border-2 border-neutral-900 shadow-[2px_2px_0px_0px_#000000]">
                        + Tambah
                    </button>
                </div>
            </form>
        </div>

        {{-- Tabel Filter Kata --}}
        <div class="bg-white rounded-3xl border-2 border-neutral-900 shadow-[4px_4px_0px_0px_#000000] overflow-hidden">
            <div class="p-4 bg-neutral-100 border-b-2 border-neutral-900 flex items-center justify-between">
                <h3 class="font-black text-sm uppercase text-neutral-900">Daftar Kata Badword Database</h3>
                <span class="text-xs font-bold text-neutral-600">{{ count($badWords) }} kata terdaftar</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b-2 border-neutral-900 bg-neutral-50 text-xs font-black uppercase text-neutral-600 tracking-wider">
                            <th class="py-3 px-4">No</th>
                            <th class="py-3 px-4">Kata / Frasa</th>
                            <th class="py-3 px-4">Bahasa</th>
                            <th class="py-3 px-4">Severity</th>
                            <th class="py-3 px-4">Tipe</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 text-sm font-semibold">
                        @forelse($badWords as $index => $bw)
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="py-3 px-4 text-neutral-500 font-bold w-12">{{ $index + 1 }}</td>
                                <td class="py-3 px-4 font-black text-neutral-900">
                                    <span class="bg-neutral-100 border border-neutral-300 px-3 py-1 rounded-full text-xs font-mono">
                                        {{ $bw->word }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-xs font-bold uppercase text-neutral-600">
                                    {{ $bw->language ?? 'indonesian' }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase {{ $bw->severity === 'high' ? 'bg-red-100 text-red-800' : ($bw->severity === 'low' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $bw->severity ?? 'medium' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-xs font-bold text-neutral-500">
                                    {{ $bw->type === 'builtin' ? '📦 Built-in' : '✍️ Custom' }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($bw->is_active)
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300">Aktif</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-neutral-100 text-neutral-500 border border-neutral-300">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('admin.komentar.badWords.toggle', $bw->id) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1 text-xs font-bold rounded-lg transition-colors {{ $bw->is_active ? 'text-amber-800 bg-amber-100 hover:bg-amber-200' : 'text-emerald-800 bg-emerald-100 hover:bg-emerald-200' }}">
                                                {{ $bw->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>

                                        <button type="button"
                                                data-word="{{ $bw->word }}"
                                                data-language="{{ $bw->language }}"
                                                data-severity="{{ $bw->severity }}"
                                                data-category="{{ $bw->category }}"
                                                data-update-url="{{ route('admin.komentar.badWords.update', $bw->id) }}"
                                                @click="editBadWordWord = $el.dataset.word; editBadWordLanguage = $el.dataset.language; editBadWordSeverity = $el.dataset.severity; editBadWordCategory = $el.dataset.category; editBadWordUrl = $el.dataset.updateUrl; editBadWordModalOpen = true"
                                                class="px-3 py-1 text-xs font-bold text-blue-700 bg-blue-50 border border-blue-300 rounded-lg hover:bg-blue-100">
                                            Edit
                                        </button>

                                        <form method="POST" action="{{ route('admin.komentar.badWords.destroy', $bw->id) }}" class="inline" onsubmit="return confirm('Hapus kata ini dari filter?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 text-xs font-bold text-red-700 bg-red-50 border border-red-300 rounded-lg hover:bg-red-100">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-neutral-500 font-bold">
                                    Belum ada kata terlarang dalam database.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ─── TAB 3: MANAGEMENT WHITELIST ─────────────────────────────── --}}
    <div x-show="activeTab === 'whitelist'" class="space-y-6">
        <div class="bg-white p-6 rounded-3xl border-2 border-neutral-900 shadow-[4px_4px_0px_0px_#000000]">
            <h3 class="text-lg font-black uppercase text-neutral-900 mb-4 flex items-center gap-2">
                <span>✅</span> Tambah Exception Whitelist
            </h3>
            <form method="POST" action="{{ route('admin.komentar.whitelists.store') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3">
                @csrf
                <div class="md:col-span-5">
                    <label class="block text-xs font-black uppercase text-neutral-600 mb-1">Frasa / Kata Whitelist</label>
                    <input type="text" name="phrase" required placeholder="misal: mie ayam babi, babi guling..." class="w-full bg-neutral-50 border-2 border-neutral-900 rounded-xl px-3.5 py-2 text-xs font-bold">
                </div>
                <div class="md:col-span-5">
                    <label class="block text-xs font-black uppercase text-neutral-600 mb-1">Alasan Pengecualian</label>
                    <input type="text" name="reason" placeholder="Nama varian produk kuliner..." class="w-full bg-neutral-50 border-2 border-neutral-900 rounded-xl px-3.5 py-2 text-xs font-bold">
                </div>
                <div class="md:col-span-2 flex items-end">
                    <button type="submit" class="w-full bg-[#FF5722] hover:bg-[#E65100] text-white font-black text-xs uppercase px-4 py-2.5 rounded-xl border-2 border-neutral-900 shadow-[2px_2px_0px_0px_#000000]">
                        + Tambah
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-3xl border-2 border-neutral-900 shadow-[4px_4px_0px_0px_#000000] overflow-hidden">
            <div class="p-4 bg-neutral-100 border-b-2 border-neutral-900 flex items-center justify-between">
                <h3 class="font-black text-sm uppercase text-neutral-900">Daftar Pengecualian Whitelist</h3>
                <span class="text-xs font-bold text-neutral-600">{{ count($whitelists) }} frasa terdaftar</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b-2 border-neutral-900 bg-neutral-50 text-xs font-black uppercase text-neutral-600 tracking-wider">
                            <th class="py-3 px-4">No</th>
                            <th class="py-3 px-4">Frasa Whitelist</th>
                            <th class="py-3 px-4">Alasan Pengecualian</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 text-sm font-semibold">
                        @forelse($whitelists as $index => $wl)
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="py-3 px-4 text-neutral-500 font-bold w-12">{{ $index + 1 }}</td>
                                <td class="py-3 px-4 font-black text-neutral-900">
                                    <span class="bg-emerald-50 border border-emerald-300 px-3 py-1 rounded-full text-xs font-mono text-emerald-900">
                                        {{ $wl->phrase }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-xs font-bold text-neutral-600">
                                    {{ $wl->reason ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($wl->is_active)
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300">Aktif</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-neutral-100 text-neutral-500 border border-neutral-300">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('admin.komentar.whitelists.toggle', $wl->id) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1 text-xs font-bold rounded-lg transition-colors {{ $wl->is_active ? 'text-amber-800 bg-amber-100 hover:bg-amber-200' : 'text-emerald-800 bg-emerald-100 hover:bg-emerald-200' }}">
                                                {{ $wl->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.komentar.whitelists.destroy', $wl->id) }}" class="inline" onsubmit="return confirm('Hapus frasa ini dari whitelist?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 text-xs font-bold text-red-700 bg-red-50 border border-red-300 rounded-lg hover:bg-red-100">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-neutral-500 font-bold">
                                    Belum ada frasa whitelist terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ─── MODAL DETAIL KOMENTAR ────────────────────────────────────── --}}
    <div x-show="detailModalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div @click.away="detailModalOpen = false" class="w-full max-w-lg bg-white rounded-3xl border-3 border-neutral-900 p-6 shadow-[6px_6px_0px_0px_#000000] relative space-y-4">
            <div class="flex items-center justify-between border-b-2 border-neutral-900 pb-3">
                <h3 class="text-lg font-black uppercase text-neutral-900">Detail Komentar</h3>
                <button @click="detailModalOpen = false" class="text-neutral-500 hover:text-neutral-900 font-black text-2xl">&times;</button>
            </div>

            <template x-if="selectedKomentar">
                <div class="space-y-4 text-sm">
                    <div class="grid grid-cols-2 gap-3 bg-[#FFF8E8] p-3.5 rounded-2xl border-2 border-neutral-900 text-xs">
                        <div>
                            <span class="font-black text-neutral-500 uppercase block">Produk</span>
                            <span class="font-black text-neutral-900 text-sm" x-text="selectedKomentar.produk"></span>
                        </div>
                        <div>
                            <span class="font-black text-neutral-500 uppercase block">User</span>
                            <span class="font-black text-neutral-900 text-sm" x-text="selectedKomentar.user"></span>
                        </div>
                        <div>
                            <span class="font-black text-neutral-500 uppercase block">Tanggal</span>
                            <span class="font-bold text-neutral-800" x-text="selectedKomentar.tanggal"></span>
                        </div>
                        <div>
                            <span class="font-black text-neutral-500 uppercase block">Status Moderasi</span>
                            <span class="font-black uppercase px-2 py-0.5 rounded text-[10px]"
                                  :class="{
                                      'bg-emerald-100 text-emerald-800 border border-emerald-300': selectedKomentar.status === 'approved',
                                      'bg-amber-100 text-amber-800 border border-amber-300': selectedKomentar.status === 'filtered',
                                      'bg-rose-100 text-rose-800 border border-rose-300': selectedKomentar.status === 'rejected'
                                  }"
                                  x-text="selectedKomentar.status">
                            </span>
                        </div>
                    </div>

                    {{-- Info Deteksi --}}
                    <div class="bg-amber-50 border-2 border-amber-300 p-3.5 rounded-2xl text-xs font-bold text-amber-900 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-black uppercase">⚠️ Deteksi Badword:</span>
                            <span class="font-extrabold uppercase px-2 py-0.5 rounded bg-amber-200" x-text="'Severity ' + selectedKomentar.severity"></span>
                        </div>
                        <p x-text="'Kata terdeteksi: ' + (selectedKomentar.detected_words || 'Tidak ada')"></p>
                    </div>

                    {{-- Komentar Asli --}}
                    <div>
                        <label class="text-xs font-black uppercase text-neutral-600 block mb-1">Komentar Asli User:</label>
                        <div class="bg-neutral-50 border-2 border-neutral-900 p-3.5 rounded-2xl font-bold text-neutral-900 leading-relaxed" x-text="selectedKomentar.komentar"></div>
                    </div>

                    {{-- Versi Disensor jika ada --}}
                    <template x-if="selectedKomentar.display_content">
                        <div>
                            <label class="text-xs font-black uppercase text-amber-700 block mb-1">Versi Tampil Disensor Admin:</label>
                            <div class="bg-amber-50 border-2 border-amber-300 p-3.5 rounded-2xl font-bold text-amber-900 leading-relaxed" x-text="selectedKomentar.display_content"></div>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Action Buttons --}}
            <div class="pt-3 border-t-2 border-neutral-900 flex items-center justify-end gap-2">
                <template x-if="selectedKomentar">
                    <div class="flex items-center gap-2">
                        <form method="POST" :action="'/admin/komentar/' + selectedKomentar.id + '/approve'" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-3.5 py-2 rounded-xl text-xs font-black uppercase bg-[#2E7D32] text-white border-2 border-neutral-900 shadow-[2px_2px_0px_0px_#000000]">
                                ✓ Tampilkan
                            </button>
                        </form>
                        <button type="button"
                                @click="detailModalOpen = false; openSensorPreview(selectedKomentar)"
                                class="px-3.5 py-2 rounded-xl text-xs font-black uppercase bg-[#FF9800] text-white border-2 border-neutral-900 shadow-[2px_2px_0px_0px_#000000]">
                            ✦ Sensor
                        </button>
                        <form method="POST" :action="'/admin/komentar/' + selectedKomentar.id + '/reject'" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-3.5 py-2 rounded-xl text-xs font-black uppercase bg-[#C62828] text-white border-2 border-neutral-900 shadow-[2px_2px_0px_0px_#000000]">
                                ✕ Tolak
                            </button>
                        </form>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ─── MODAL SENSOR PREVIEW & CONFIRMATION ──────────────────────── --}}
    <div x-show="censorModalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div @click.away="censorModalOpen = false" class="w-full max-w-lg bg-white rounded-3xl border-3 border-neutral-900 p-6 shadow-[6px_6px_0px_0px_#000000] relative space-y-4">
            <div class="flex items-center justify-between border-b-2 border-neutral-900 pb-3">
                <h3 class="text-lg font-black uppercase text-neutral-900 flex items-center gap-2">
                    <span>✦</span> Sensor Komentar User
                </h3>
                <button @click="censorModalOpen = false" class="text-neutral-500 hover:text-neutral-900 font-black text-2xl">&times;</button>
            </div>

            <template x-if="selectedKomentar">
                <form method="POST" :action="'/admin/komentar/' + selectedKomentar.id + '/censor'" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <p class="text-xs font-bold text-neutral-600">
                        Komentar asli akan **tetap tersimpan utuh** di database. Versi yang disensor akan disimpan ke <code class="bg-neutral-100 px-1 py-0.5 rounded text-neutral-900 font-mono">display_content</code> untuk ditampilkan publik.
                    </p>

                    {{-- ASLI --}}
                    <div>
                        <label class="text-xs font-black uppercase text-neutral-500 block mb-1">ASLI (Tersimpan Utuh di Database):</label>
                        <div class="bg-neutral-100 border-2 border-neutral-900 p-3.5 rounded-2xl font-bold text-neutral-800 text-xs" x-text="selectedKomentar.komentar"></div>
                    </div>

                    {{-- HASIL SENSOR PREVIEW / EDITABLE --}}
                    <div>
                        <label class="text-xs font-black uppercase text-[#FF5722] block mb-1">HASIL SENSOR (Tampil ke Publik):</label>
                        <textarea name="display_content"
                                  rows="3"
                                  x-model="selectedKomentar.auto_censored"
                                  class="w-full bg-[#FFF8E8] border-2 border-neutral-900 p-3.5 rounded-2xl font-black text-neutral-900 text-xs focus:outline-none focus:bg-white"></textarea>
                    </div>

                    <div class="pt-3 border-t-2 border-neutral-900 flex items-center justify-end gap-2">
                        <button type="button" @click="censorModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-bold bg-neutral-200 hover:bg-neutral-300 text-neutral-800 border-2 border-neutral-900">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl text-xs font-black uppercase bg-[#FF5722] hover:bg-[#E65100] text-white border-2 border-neutral-900 shadow-[2px_2px_0px_0px_#000000]">
                            Simpan & Tampilkan
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    {{-- ─── MODAL EDIT BAD WORD ───────────────────────────────────────── --}}
    <div x-show="editBadWordModalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div @click.away="editBadWordModalOpen = false; editBadWordUrl = ''" class="w-full max-w-md bg-white rounded-3xl border-3 border-neutral-900 p-6 shadow-[6px_6px_0px_0px_#000000] relative space-y-4">
            <h3 class="text-lg font-black uppercase text-neutral-900 border-b-2 border-neutral-900 pb-3">Edit Kata Filter</h3>
            <form method="POST" :action="editBadWordUrl ? editBadWordUrl : '#'" class="space-y-4" @submit="if(!editBadWordUrl) $event.preventDefault()">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-xs font-black uppercase text-neutral-600 mb-1">Kata / Frasa</label>
                    <input type="text" name="word" x-model="editBadWordWord" required class="w-full bg-neutral-50 border-2 border-neutral-900 rounded-xl px-3.5 py-2 text-xs font-bold">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase text-neutral-600 mb-1">Bahasa</label>
                    <select name="language" x-model="editBadWordLanguage" class="w-full bg-neutral-50 border-2 border-neutral-900 rounded-xl px-3 py-2 text-xs font-bold">
                        <option value="indonesian">Indonesia</option>
                        <option value="english">English</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black uppercase text-neutral-600 mb-1">Severity</label>
                    <select name="severity" x-model="editBadWordSeverity" class="w-full bg-neutral-50 border-2 border-neutral-900 rounded-xl px-3 py-2 text-xs font-bold">
                        <option value="low">Ringan (Low)</option>
                        <option value="medium">Normal (Medium)</option>
                        <option value="high">Ketat (High)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black uppercase text-neutral-600 mb-1">Kategori</label>
                    <input type="text" name="category" x-model="editBadWordCategory" class="w-full bg-neutral-50 border-2 border-neutral-900 rounded-xl px-3.5 py-2 text-xs font-bold">
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t-2 border-neutral-900">
                    <button type="button" @click="editBadWordModalOpen = false; editBadWordUrl = ''" class="px-4 py-2 rounded-xl text-xs font-bold bg-neutral-200 border-2 border-neutral-900">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-black uppercase bg-[#FF5722] text-white border-2 border-neutral-900 shadow-[2px_2px_0px_0px_#000000]">Update</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
