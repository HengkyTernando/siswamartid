@extends('layouts.public')

@section('title', 'SiswaMart')
@section('meta_description', 'SiswaMart — Temukan produk kreatif siswa: makanan, minuman, snack, dan berbagai produk karya siswa di lingkungan sekolah.')

@section('content')

<section id="mobile-product-dashboard" class="md:hidden max-w-[1400px] mx-auto px-5 pt-6 pb-14" aria-label="Katalog produk siswa">
    <div class="mobile-home-hero relative mb-6 pt-0">
        <p class="mb-3 text-[15px] font-extrabold uppercase tracking-wide text-[var(--color-primary-dark)]">Karya siswa SMKN 11</p>
        <img src="{{ asset('images/food.png') }}" alt="" class="mobile-home-hero__art" aria-hidden="true">
        <h1 class="mobile-home-hero__title font-extrabold uppercase text-[var(--color-text)]">
            Makan <span class="text-[var(--color-primary)]">enak.</span><br>
            Buatan teman<br>sendiri.
        </h1>
        <p class="mt-3 text-[15px] font-medium leading-relaxed text-[var(--color-text-muted)]" style="max-width:calc(100% - 5.5rem)">
            Temukan jajanan, makanan berat, dan minuman favorit dari teman-teman di sekolahmu.
        </p>

        <div class="mt-4 flex items-center gap-3" style="border-radius:16px; border:3px solid var(--color-border); padding:10px 14px; box-shadow:0 3px 0 0 var(--color-border);">
            <svg class="h-6 w-6 shrink-0 text-[var(--color-text)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/>
            </svg>
            <p class="text-sm font-extrabold leading-snug text-[var(--color-text)]">Untuk saat ini, SiswaMart melayani penjualan di area SMKN 11.</p>
        </div>
    </div>

    <section class="mb-8" aria-labelledby="mobile-category-title">
        <div class="mb-3 flex items-center justify-between gap-3">
            <h2 id="mobile-category-title" class="text-[19px] font-extrabold uppercase tracking-tight text-[var(--color-text)]">Pilih kategorimu</h2>
            <a href="{{ route('public.kategori.index') }}" class="shrink-0 text-sm font-extrabold text-[var(--color-primary-dark)]">Semua</a>
        </div>
        <div class="grid grid-cols-2 gap-3">
            @foreach($kategoriList->take(4) as $kat)
                @php
                    $mobileCategoryData = [
                        'dessert'       => ['icon' => 'dessert.svg', 'color' => '#D4C4FB'],
                        'makanan-berat' => ['icon' => 'food.svg',    'color' => '#F4A57C'],
                        'makanan'       => ['icon' => 'food.svg',    'color' => '#F4A57C'],
                        'minuman'       => ['icon' => 'drink.svg',   'color' => '#7DCFB6'],
                        'snack'         => ['icon' => 'snack.svg',   'color' => '#FFD84D'],
                    ];
                    $mobileCategory = $mobileCategoryData[$kat->slug] ?? ['icon' => 'bag.svg', 'color' => '#FFF8E8'];
                @endphp
                <a href="{{ route('public.kategori.show', $kat->slug) }}" class="mobile-category-card flex min-h-[142px] flex-col justify-end rounded-[22px] border-[3px] border-[var(--color-border)] px-4 py-4 shadow-[0_4px_0_0_var(--color-border)] transition-transform active:translate-y-1" style="background-color:{{ $mobileCategory['color'] }}">
                    <img src="{{ asset('images/icons/' . $mobileCategory['icon']) }}" alt="" aria-hidden="true" class="mb-3 h-12 w-12 object-contain">
                    <span class="text-[15px] font-extrabold uppercase leading-tight text-[var(--color-text)]">{{ $kat->nama }}</span>
                    <span class="mt-1 text-sm font-bold text-[var(--color-text)]">{{ $kat->produk_aktif_count }} produk</span>
                </a>
            @endforeach
        </div>
    </section>

    <form action="{{ route('public.home') }}" method="GET" class="mb-4 hidden flex gap-2">
        @if($selectedKategori)<input type="hidden" name="kategori" value="{{ $selectedKategori }}">@endif
        <label for="mobile-product-search" class="sr-only">Cari produk</label>
        <input id="mobile-product-search" type="search" name="q" value="{{ $search }}" placeholder="Cari produk atau toko"
               class="search-input min-w-0 flex-1 bg-white px-4 py-3 text-sm font-semibold text-[var(--color-text)] placeholder:text-[var(--color-text-muted)]">
        <button type="submit" class="btn-primary shrink-0 px-4 text-sm" aria-label="Cari produk">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-5.15a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/>
            </svg>
        </button>
    </form>

    <nav class="mb-4 hidden flex gap-2 overflow-x-auto pb-1 scrollbar-hide" aria-label="Filter kategori produk">
        <a href="{{ route('public.home', array_filter(['q' => $search])) }}"
           class="cat-chip inline-flex shrink-0 items-center gap-1.5 px-3 py-1.5 text-[11px] {{ !$selectedKategori ? 'bg-[var(--color-primary)]' : '' }}">
            <img src="{{ asset('images/icons/bag.svg') }}" alt="" class="h-4 w-4"> Semua
        </a>
        @foreach($kategoriList as $kat)
            @php
                $mobileCategoryIcons = [
                    'makanan' => 'food.svg',
                    'minuman' => 'drink.svg',
                    'snack' => 'snack.svg',
                    'dessert' => 'dessert.svg',
                ];
                $mobileCategoryIcon = $mobileCategoryIcons[$kat->slug] ?? 'bag.svg';
            @endphp
            <a href="{{ route('public.home', array_filter(['q' => $search, 'kategori' => $kat->slug])) }}"
               class="cat-chip inline-flex shrink-0 items-center gap-1.5 px-3 py-1.5 text-[11px] {{ $selectedKategori === $kat->slug ? 'bg-[var(--color-primary)]' : '' }}">
                <img src="{{ asset('images/icons/' . $mobileCategoryIcon) }}" alt="" class="h-4 w-4"> {{ $kat->nama }}
            </a>
        @endforeach
    </nav>

    @if($produkMobile->isEmpty())
        <p class="rounded-2xl border-2 border-dashed border-[var(--color-border)] bg-white px-5 py-8 text-center text-sm font-bold text-[var(--color-text-muted)]">
            Belum ada produk yang cocok. Coba kata kunci lain, ya.
        </p>
    @else
        <h2 class="mb-3 text-[19px] font-black uppercase tracking-tight text-[var(--color-text)]">Produk Populer</h2>
        <div class="grid grid-cols-2 gap-3">
            @foreach($produkMobile as $item)
                <x-public.product-card :produk="$item" />
            @endforeach
        </div>
        <div class="mt-5">{{ $produkMobile->links() }}</div>
    @endif
</section>

<div class="hidden md:block max-w-[1400px] mx-auto px-6 lg:px-[60px]">
    
    {{-- ─── 1. HERO SECTION ────────────────────────────────────────── --}}
    <div class="relative [overflow-x:clip] py-16 md:py-24 flex flex-col md:flex-row items-center justify-between gap-12 mt-4 md:mt-8 mb-12">
        {{-- Organic Blob Background --}}
        <div class="absolute top-[-10%] right-[-5%] w-[300px] h-[300px] md:w-[600px] md:h-[600px] bg-[var(--color-primary-light)] rounded-full mix-blend-multiply filter blur-3xl opacity-80 pointer-events-none z-[-1]"></div>
        
        <div class="w-full md:w-3/5 relative z-10">
            <h1 class="text-6xl md:text-7xl lg:text-[90px] font-extrabold text-[var(--color-text)] leading-[0.9] tracking-tighter mb-6 uppercase">
                MAKAN <span class="text-[var(--color-primary)]">ENAK.</span><br>
                BUATAN TEMAN SENDIRI.
            </h1>
            <p class="text-lg md:text-2xl font-bold text-[var(--color-text-muted)] max-w-xl mb-10 leading-snug">
                Temukan jajanan, makanan berat, dan minuman favorit dari teman-teman di sekolahmu.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('public.kategori.index') }}" class="btn-primary px-8 py-4 text-lg bg-[var(--color-primary)] text-white hover:bg-[var(--color-primary-dark)]">
                    JELAJAHI KATALOG
                </a>
            </div>
        </div>

        <div class="w-full md:w-2/5 relative z-10 hidden md:flex justify-end items-center">
            <img src="{{ asset('images/food.png') }}" alt="Logo" class="w-full max-w-[400px] h-auto object-contain drop-shadow-xl hover:scale-105 transition-transform duration-300">
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-12 items-start">
        
        {{-- ─── LEFT COLUMN: Kategori & Statistik (Span 4/12) ──────────── --}}
        <div class="w-full lg:w-4/12 flex flex-col gap-12">
            
            {{-- Kategori --}}
            <div>
                <h2 class="text-4xl font-extrabold text-[var(--color-text)] tracking-tighter uppercase mb-6">PILIH KATEGORIMU</h2>
                <div class="grid grid-cols-2 gap-4">
                    @foreach($kategoriList->take(4) as $kat)
                        @php
                            $catData = [
                                'makanan' => ['icon'=>'food.svg', 'bg'=>'bg-[#FFB084]', 'text'=>'text-[var(--color-text)]'],
                                'minuman' => ['icon'=>'drink.svg', 'bg'=>'bg-[#A3D9C9]', 'text'=>'text-[var(--color-text)]'],
                                'snack' => ['icon'=>'snack.svg', 'bg'=>'bg-[#FFD84D]', 'text'=>'text-[var(--color-text)]'],
                                'dessert' => ['icon'=>'dessert.svg', 'bg'=>'bg-[#D4C4FB]', 'text'=>'text-[var(--color-text)]'],
                            ];
                            $data = $catData[$kat->slug] ?? ['icon'=>'bag.svg', 'bg'=>'bg-[#FFF8E8]', 'text'=>'text-[var(--color-text)]'];
                        @endphp
                        <a href="{{ route('public.kategori.show', $kat->slug) }}" class="category-card flex flex-col items-center justify-center p-6 {{ $data['bg'] }} aspect-square group">
                            <div class="w-16 h-16 mb-4 drop-shadow-[2px_2px_0_rgba(36,26,20,1)] group-hover:scale-110 transition-transform duration-300">
                                <img src="{{ asset('images/icons/' . $data['icon']) }}" alt="{{ $kat->nama }}" class="w-full h-full object-contain">
                            </div>
                            <span class="text-lg font-extrabold {{ $data['text'] }} text-center uppercase">{{ $kat->nama }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Statistik Compact --}}
            <div class="bg-[var(--color-surface)] border-2 border-[var(--color-border)] rounded-[32px] p-8 shadow-solid-lg">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <div class="text-4xl font-black text-[var(--color-primary)]">{{ number_format($totalProduk) }}</div>
                        <div class="text-xs font-bold text-[var(--color-text)] uppercase tracking-widest mt-1">PRODUK</div>
                    </div>
                    <div>
                        <div class="text-4xl font-black text-[var(--color-success)]">{{ number_format($totalPenjual) }}</div>
                        <div class="text-xs font-bold text-[var(--color-text)] uppercase tracking-widest mt-1">PENJUAL</div>
                    </div>
                    <div>
                        <div class="text-4xl font-black text-[var(--color-warning)]">{{ number_format($totalKategori) }}</div>
                        <div class="text-xs font-bold text-[var(--color-text)] uppercase tracking-widest mt-1">KATEGORI</div>
                    </div>
                    <div class="flex items-center">
                        <a href="{{ route('public.kategori.index') }}" class="text-lg font-black text-[var(--color-text)] hover:text-[var(--color-primary)] flex items-center gap-2 group">
                            LIHAT KATALOG 
                            <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

        </div>

        {{-- ─── RIGHT COLUMN: Produk Rekomendasi (Span 8/12) ────────────── --}}
        <div class="w-full lg:w-8/12">
            <div class="flex items-end justify-between mb-8">
                <h2 class="text-4xl md:text-5xl font-extrabold text-[var(--color-text)] tracking-tighter uppercase leading-none">
                    YANG LAGI <br><span class="text-[var(--color-primary)]">POPULER.</span>
                </h2>
                <a href="{{ route('public.kategori.index') }}" class="btn-primary px-6 py-2 text-sm bg-white hover:bg-[var(--color-surface)]">
                    LIHAT SEMUA PRODUK
                </a>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($produkRekomendasi->take(6) as $item)
                    <x-public.product-card :produk="$item" />
                @endforeach
            </div>
            
            @if($produkRekomendasi->isEmpty())
                <div class="bg-[var(--color-surface)] border-2 border-dashed border-[var(--color-border)] rounded-3xl p-12 text-center text-[var(--color-text-muted)] font-bold uppercase">
                    Belum ada produk untuk ditampilkan saat ini.
                </div>
            @endif
        </div>

    </div>

</div>

<section aria-label="Cara Memesan" class="[overflow-x:clip] max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-[60px] pb-20">
    <div class="mt-12 md:mt-24 bg-[var(--color-success)] border-2 border-[var(--color-border)] rounded-[40px] p-6 sm:p-8 md:p-16 shadow-solid-lg text-white">
        <div class="flex flex-col lg:flex-row gap-8 md:gap-12 items-center justify-between">
            <div class="w-full lg:w-1/3 text-center lg:text-left">
                <h2 class="text-4xl sm:text-5xl md:text-6xl font-black uppercase tracking-tighter leading-none mb-4 drop-shadow-[2px_2px_0_rgba(36,26,20,1)]">
                    <span class="md:hidden">CARA MEMESAN.</span>
                    <span class="hidden md:inline">TEMUKAN<br>PRODUK SISWA.</span>
                </h2>
                <p class="text-base sm:text-lg font-bold opacity-90 max-w-sm mx-auto lg:mx-0">
                    Jelajahi produk siswa dan lanjutkan komunikasi langsung dengan penjual melalui WhatsApp.
                </p>
                <a href="{{ route('public.cara_pemesanan') }}" class="btn-primary inline-flex mt-6 md:mt-8 px-6 sm:px-8 py-3 bg-[var(--color-bg)] text-[var(--color-text)] hover:bg-[var(--color-warning)] text-sm border-2 border-[var(--color-border)] shadow-solid-sm">
                    LIHAT CARANYA
                </a>
            </div>
            
            <div class="w-full lg:w-2/3 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white text-[var(--color-text)] p-5 sm:p-6 rounded-[24px] border-2 border-[var(--color-border)] shadow-solid-sm transform -rotate-1 hover:rotate-0 transition-transform">
                    <div class="text-4xl font-black text-[var(--color-primary)] mb-2">01</div>
                    <div class="text-lg sm:text-xl font-extrabold uppercase">Pilih Produk</div>
                </div>
                <div class="bg-white text-[var(--color-text)] p-5 sm:p-6 rounded-[24px] border-2 border-[var(--color-border)] shadow-solid-sm transform rotate-1 hover:rotate-0 transition-transform">
                    <div class="text-4xl font-black text-[var(--color-warning)] mb-2">02</div>
                    <div class="text-lg sm:text-xl font-extrabold uppercase">Baca Ulasan</div>
                </div>
                <div class="bg-white text-[var(--color-text)] p-5 sm:p-6 rounded-[24px] border-2 border-[var(--color-border)] shadow-solid-sm transform rotate-2 hover:rotate-0 transition-transform">
                    <div class="text-4xl font-black text-[var(--color-success)] mb-2">03</div>
                    <div class="text-lg sm:text-xl font-extrabold uppercase">Hubungi Penjual</div>
                </div>
                <div class="bg-white text-[var(--color-text)] p-5 sm:p-6 rounded-[24px] border-2 border-[var(--color-border)] shadow-solid-sm transform -rotate-2 hover:rotate-0 transition-transform">
                    <div class="text-4xl font-black text-[var(--color-text-muted)] mb-2">04</div>
                    <div class="text-lg sm:text-xl font-extrabold uppercase">Atur Langsung</div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
