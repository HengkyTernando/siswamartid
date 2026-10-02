<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <meta name="description" content="@yield('meta_description', 'SiswaMart — Media promosi produk siswa. Temukan makanan, minuman, snack, dan produk kreatif karya siswa sekolah.')">
    <title>@yield('title', 'SiswaMart')</title>
    <style>[x-cloak] { display: none !important; }</style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[var(--color-bg)] text-[var(--color-text)] min-h-screen antialiased flex flex-col"
      x-data="{ mobileMenuOpen: false }">

{{-- ─── Navbar ──────────────────────────────────────────────────────── --}}
<header style="position: fixed; top: 16px; left: 50%; transform: translateX(-50%); z-index: 99999; pointer-events: auto;"
        class="w-[calc(100vw-32px)] md:w-max max-w-[1400px]"
        x-data="{ mobileMenuOpen: false, searchOpen: false }">
    <div :class="(mobileMenuOpen || searchOpen) ? 'rounded-2xl sm:rounded-3xl' : 'rounded-full'"
         class="bg-[var(--color-primary)] shadow-[0_8px_30px_rgba(0,0,0,0.18)] px-4 sm:px-8 py-2.5 sm:py-3 border-0 transition-all duration-200 w-full">

        {{-- Desktop Navigation Row --}}
        <div class="hidden md:flex items-center gap-5 lg:gap-7">
            {{-- 1. Beranda --}}
            <a href="{{ route('public.home') }}"
               id="nav-beranda"
               class="text-sm lg:text-base font-extrabold tracking-wide transition-colors {{ request()->routeIs('public.home') ? 'text-neutral-900 bg-white/20 px-3 py-1 rounded-full' : 'text-white hover:text-neutral-900' }}">
                Beranda
            </a>

            {{-- 2. Kategori --}}
            <a href="{{ route('public.kategori.index') }}"
               id="nav-kategori"
               class="text-sm lg:text-base font-extrabold tracking-wide transition-colors {{ request()->routeIs('public.kategori.*') ? 'text-neutral-900 bg-white/20 px-3 py-1 rounded-full' : 'text-white hover:text-neutral-900' }}">
                Kategori
            </a>

            {{-- 3. SiswaMart Text Logo (Siswa Hitam, Mart Orange) --}}
            <a href="{{ route('public.home') }}"
               id="nav-logo"
               class="px-3.5 py-1 bg-white rounded-full shadow-sm text-base lg:text-lg font-black tracking-tight hover:scale-105 transition-all inline-flex items-center"
               aria-label="SiswaMart — Beranda">
                <span class="text-neutral-900">Siswa</span><span class="text-[var(--color-primary)]">Mart</span>
            </a>

            {{-- 4. Cara Memesan --}}
            <a href="{{ route('public.cara_pemesanan') }}"
               id="nav-cara-pemesanan"
               class="text-sm lg:text-base font-extrabold tracking-wide transition-colors {{ request()->routeIs('public.cara_pemesanan') ? 'text-neutral-900 bg-white/20 px-3 py-1 rounded-full' : 'text-white hover:text-neutral-900' }}">
                Cara Memesan
            </a>

            {{-- 5. Tentang Kami --}}
            <a href="{{ route('public.tentang_kami') }}"
               id="nav-tentang-kami"
               class="text-sm lg:text-base font-extrabold tracking-wide transition-colors {{ request()->routeIs('public.tentang_kami') ? 'text-neutral-900 bg-white/20 px-3 py-1 rounded-full' : 'text-white hover:text-neutral-900' }}">
                Tentang Kami
            </a>

            {{-- 6. Search Icon & Inline Expanding Search Bar --}}
            @php
                $searchAction = route('public.kategori.index');
                if (request()->routeIs('public.kategori.*')) {
                    $searchAction = url()->current();
                }
            @endphp

            <div class="relative flex items-center"
                 @keydown.escape.window="if('{{ request('q') }}' === '') searchOpen = false">

                {{-- Collapsed Search Icon Button --}}
                <button x-show="!searchOpen && '{{ request('q') }}' === ''"
                        @click="searchOpen = true; $nextTick(() => $refs.searchInput?.focus())"
                        type="button"
                        class="p-2 text-white hover:text-neutral-900 hover:bg-white/20 rounded-full transition-colors flex items-center justify-center"
                        aria-label="Cari produk">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>

                {{-- Expanded Inline Search Bar --}}
                <div x-show="searchOpen || '{{ request('q') }}' !== ''"
                     @click.away="if('{{ request('q') }}' === '') searchOpen = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="relative flex items-center"
                     style="{{ request('q') ? '' : 'display: none;' }}">
                    <form action="{{ $searchAction }}" method="GET" id="nav-search-form" class="relative flex items-center w-48 sm:w-56 lg:w-60" style="position: relative;">
                        @if(request('min_price')) <input type="hidden" name="min_price" value="{{ request('min_price') }}"> @endif
                        @if(request('max_price')) <input type="hidden" name="max_price" value="{{ request('max_price') }}"> @endif
                        @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                        @if(request('kategori') && !request()->routeIs('public.kategori.*')) <input type="hidden" name="kategori" value="{{ request('kategori') }}"> @endif

                        <svg class="absolute w-4 h-4 text-neutral-400 pointer-events-none" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%);" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input x-ref="searchInput"
                               type="text"
                               name="q"
                               id="nav-search-input"
                               placeholder="Cari jajanan, makanan, atau minuman..."
                               value="{{ request('q') }}"
                                   class="w-full py-1.5 text-xs sm:text-sm text-neutral-900 bg-white rounded-full border border-black/10 outline-none shadow-sm focus:ring-2 focus:ring-black/20 transition-all font-medium"
                                   style="padding-left: 36px; padding-right: 34px;">

                        <button @click="if('{{ request('q') }}' === '') searchOpen = false; else window.location.href='{{ $searchAction }}';"
                                type="button"
                                class="text-neutral-400 hover:text-neutral-700 p-0.5 rounded-full"
                                style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); display: flex; align-items: center; justify-content: center;"
                                aria-label="Tutup pencarian">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- 7. Dashboard / Auth Button --}}
            @auth
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}"
                       id="nav-admin-dashboard"
                       class="text-xs sm:text-sm font-extrabold text-white bg-neutral-900 hover:bg-black px-4 py-2 rounded-full transition-colors shadow-sm">
                        Dashboard
                    </a>
                @elseif(auth()->user()->role === 'penjual')
                    <a href="{{ route('penjual.dashboard') }}"
                       id="nav-penjual-dashboard"
                       class="text-xs sm:text-sm font-extrabold text-white bg-neutral-900 hover:bg-black px-4 py-2 rounded-full transition-colors shadow-sm">
                        Toko Saya
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}"
                   id="nav-login"
                   class="text-xs sm:text-sm font-extrabold text-white bg-neutral-900 hover:bg-black px-4 py-2 rounded-full transition-colors shadow-sm">
                    Masuk
                </a>
            @endauth
        </div>

        {{-- Mobile Bar --}}
        <div class="flex md:hidden items-center justify-between gap-3 w-full">
            <a href="{{ route('public.home') }}"
               class="px-3.5 py-1 bg-white rounded-full text-sm font-black tracking-tight text-neutral-900 shadow-sm">
                <span class="text-neutral-900">Siswa</span><span class="text-[var(--color-primary)]">Mart</span>
            </a>

            <div class="flex items-center gap-1.5">
                <button @click="searchOpen = !searchOpen; if(searchOpen) mobileMenuOpen = false"
                        type="button"
                        class="p-2 text-white hover:bg-white/20 rounded-full transition-colors"
                        aria-label="Cari">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>

                <button @click="mobileMenuOpen = !mobileMenuOpen; if(mobileMenuOpen) searchOpen = false"
                        class="p-2 text-white hover:bg-white/20 rounded-full transition-colors"
                        id="mobile-menu-btn"
                        aria-label="Buka menu navigasi">
                    <svg x-show="!mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Search Dropdown --}}
        <div x-show="searchOpen" x-transition class="md:hidden pt-3 border-t border-white/20 mt-2 text-neutral-900" style="display:none;">
            <form action="{{ $searchAction }}" method="GET" id="mobile-nav-search-form" class="relative">
                @if(request('min_price')) <input type="hidden" name="min_price" value="{{ request('min_price') }}"> @endif
                @if(request('max_price')) <input type="hidden" name="max_price" value="{{ request('max_price') }}"> @endif
                @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                @if(request('kategori') && !request()->routeIs('public.kategori.*')) <input type="hidden" name="kategori" value="{{ request('kategori') }}"> @endif

                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="q" placeholder="Cari jajanan, makanan, atau minuman..." value="{{ request('q') }}"
                       class="w-full pl-9 pr-3 py-2 text-xs text-neutral-800 bg-white border border-neutral-200 rounded-lg outline-none focus:bg-white focus:border-neutral-400">
            </form>
        </div>

        {{-- Mobile Navigation Dropdown --}}
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="md:hidden pt-3 pb-1 border-t border-white/20 mt-2.5 space-y-1 text-white"
             style="display:none;">
            <a href="{{ route('public.home') }}"
               class="flex items-center px-4 py-2.5 rounded-xl text-sm font-extrabold transition-colors {{ request()->routeIs('public.home') ? 'bg-white text-[var(--color-primary)]' : 'text-white hover:bg-white/15' }}"
               id="mobile-nav-beranda">Beranda</a>
            <a href="{{ route('public.kategori.index') }}"
               class="flex items-center px-4 py-2.5 rounded-xl text-sm font-extrabold transition-colors {{ request()->routeIs('public.kategori.*') ? 'bg-white text-[var(--color-primary)]' : 'text-white hover:bg-white/15' }}"
               id="mobile-nav-kategori">Kategori</a>
            <a href="{{ route('public.cara_pemesanan') }}"
               class="flex items-center px-4 py-2.5 rounded-xl text-sm font-extrabold transition-colors {{ request()->routeIs('public.cara_pemesanan') ? 'bg-white/20 text-white font-extrabold' : 'text-white hover:bg-white/15' }}"
               id="mobile-nav-cara-pemesanan">Cara Memesan</a>
            <a href="{{ route('public.tentang_kami') }}"
               class="flex items-center px-4 py-2.5 rounded-xl text-sm font-extrabold transition-colors {{ request()->routeIs('public.tentang_kami') ? 'bg-white/20 text-white font-extrabold' : 'text-white hover:bg-white/15' }}"
               id="mobile-nav-tentang-kami">Tentang Kami</a>

            @auth
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}"
                       id="mobile-nav-admin-dashboard"
                       class="flex items-center justify-center px-4 py-2.5 mt-2 rounded-xl text-sm font-extrabold text-white bg-neutral-900 hover:bg-black transition-colors shadow-sm">
                        Dashboard Admin
                    </a>
                @elseif(auth()->user()->role === 'penjual')
                    <a href="{{ route('penjual.dashboard') }}"
                       id="mobile-nav-penjual-dashboard"
                       class="flex items-center justify-center px-4 py-2.5 mt-2 rounded-xl text-sm font-extrabold text-white bg-neutral-900 hover:bg-black transition-colors shadow-sm">
                        Toko Saya
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}"
                   id="mobile-nav-login"
                   class="flex items-center justify-center px-4 py-2.5 mt-2 rounded-xl text-sm font-extrabold text-white bg-neutral-900 hover:bg-black transition-colors shadow-sm">
                    Masuk
                </a>
            @endauth
        </div>
    </div>
</header>

{{-- ─── Page Content ───────────────────────────────────────────────── --}}
<main class="flex-grow pt-24 sm:pt-28">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-[60px] mb-5 {{ request()->routeIs('public.home') ? 'hidden md:block' : '' }}">
        <div class="flex items-center gap-3 rounded-2xl border-2 border-[var(--color-border)] bg-[#A3D9C9] px-4 py-3 sm:px-5 shadow-solid-sm">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border-2 border-[var(--color-border)] bg-white text-lg" aria-hidden="true">⌖</span>
            <p class="text-xs sm:text-sm font-bold leading-snug text-[var(--color-text)]">Untuk saat ini, SiswaMart melayani penjualan di area SMKN 11.</p>
        </div>
    </div>
    @yield('content')
</main>

{{-- ─── Footer ─────────────────────────────────────────────────────── --}}
<footer class="bg-[var(--color-text)] text-[var(--color-bg)] border-t-2 border-[var(--color-border)] mt-20">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-[60px] py-16">
        <div class="flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="text-center md:text-left">
                <h2 class="text-3xl font-extrabold mb-2 text-[var(--color-primary-border)]">SiswaMart.</h2>
                <p class="text-lg font-medium opacity-90 max-w-sm">
                    Media promosi produk karya siswa SMKN 11. Hubungi penjual secara langsung melalui WhatsApp.
                </p>
            </div>
            <div class="flex gap-6 font-bold text-sm">
                <a href="{{ route('public.home') }}" class="hover:text-[var(--color-primary-border)] transition-colors">BERANDA</a>
                <a href="{{ route('public.kategori.index') }}" class="hover:text-[var(--color-primary-border)] transition-colors">KATEGORI</a>
                <a href="{{ route('public.cara_pemesanan') }}" class="hover:text-[var(--color-primary-border)] transition-colors">CARA MEMESAN</a>
                <a href="{{ route('public.tentang_kami') }}" class="hover:text-[var(--color-primary-border)] transition-colors">TENTANG KAMI</a>
            </div>
        </div>
    </div>
</footer>

@stack('modals')

</body>
</html>
