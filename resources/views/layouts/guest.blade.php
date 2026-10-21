<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SiswaMart') }} - Login</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&display=swap">

    <link rel="stylesheet" href="{{ asset('css/siswamart-theme.css') }}">
</head>
<body class="auth-page {{ request()->routeIs('login', 'password.*', 'verification.*') ? 'auth-page--login' : '' }} antialiased min-h-screen flex flex-col justify-center items-center p-4">

    <!-- Removed Theme Toggle -->

    @unless (request()->routeIs('login', 'password.*', 'verification.*'))
    <nav class="auth-site-nav" aria-label="Navigasi utama">
        <a href="{{ route('public.home') }}" class="auth-site-logo" aria-label="SiswaMart — Beranda"><span>Siswa</span><strong>Mart</strong></a>
        <div class="auth-site-links">
            <a href="{{ route('public.home') }}">Beranda</a>
            <a href="{{ route('public.kategori.index') }}">Kategori</a>
            <a href="{{ route('public.cara_pemesanan') }}">Cara Memesan</a>
            <a href="{{ route('public.tentang_kami') }}">Tentang Kami</a>
        </div>
        <a href="{{ route('public.produk.index') }}" class="auth-site-action">Jelajahi katalog</a>
    </nav>
    @endunless

    <main class="auth-shell">
        <!-- Card Container -->
        <div class="auth-card">
            {{ $slot }}
        </div>
    </main>

    @unless (request()->routeIs('login', 'password.*', 'verification.*'))
    <footer class="auth-site-footer">
        <span>© {{ date('Y') }} SiswaMart · Produk karya siswa SMKN 11 Bandung</span>
        <a href="https://wa.me/6285724494193" target="_blank" rel="noopener noreferrer">Hubungi SiswaMart · WhatsApp</a>
    </footer>
    @endunless

    <x-back-floating />

    <!-- Removed Dark Mode Script -->
</body>
</html>

