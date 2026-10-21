@extends('layouts.public')

@section('title', 'SiswaMart')
@section('meta_description', 'Jelajahi SiswaMart, katalog makanan, minuman, dan jajanan buatan siswa SMKN 11 Bandung. Temukan produk favorit dan hubungi penjual langsung via WhatsApp.')

@section('content')
    @php
        $heroRating = $produkRekomendasi->max('ulasan_avg_rating');
        $heroPrice = $produkRekomendasi->min('harga');
        $categoryColors = [
            'dessert' => 'var(--lavender)',
            'makanan' => 'var(--peach)',
            'makanan-berat' => 'var(--peach)',
            'minuman' => 'var(--mint)',
            'snack' => 'var(--butter)',
        ];
        $categoryIcons = [
            'dessert' => 'cake',
            'makanan' => 'burger',
            'makanan-berat' => 'burger',
            'minuman' => 'glass-full',
            'snack' => 'cookie',
        ];
    @endphp

    <div class="home-page-content">
        <section class="home-hero home-container" aria-labelledby="home-hero-title">
            <div class="home-hero__copy">
                <p class="home-hero__area">
                    <span class="home-hero__area-icon" aria-hidden="true"><x-public.home-icon name="map-pin" /></span>
                    <span>Untuk saat ini, SiswaMart melayani penjualan di area SMKN 11 Bandung.</span>
                </p>

                <h1 id="home-hero-title" class="home-hero__title">
                    <span>MAKAN <span class="home-hero__accent">ENAK.</span></span>
                    <span class="home-hero__title-second">BUATAN TEMAN SENDIRI.</span>
                </h1>

                <p class="home-hero__description">
                    Temukan produk makanan dan minuman karya siswa, lalu hubungi penjual langsung lewat WhatsApp.
                </p>

                <div class="home-hero__actions">
                    <a href="{{ route('public.produk.index') }}" class="home-button home-button--primary">Jelajahi katalog</a>
                    <a href="{{ route('public.cara_pemesanan') }}" class="home-button home-button--outline">Cara memesan</a>
                </div>

                <ul class="home-hero__benefits" aria-label="Keunggulan SiswaMart">
                    <li>
                        <span class="home-hero__benefit-icon" aria-hidden="true">
                            <x-public.home-icon name="brand-whatsapp" />
                        </span>
                        <span>Pesan langsung via WhatsApp</span>
                    </li>
                    <li>
                        <span class="home-hero__benefit-icon" aria-hidden="true">
                            <x-public.home-icon name="school" />
                        </span>
                        <span>Dari siswa SMKN 11</span>
                    </li>
                    <li>
                        <span class="home-hero__benefit-icon" aria-hidden="true">
                            <x-public.home-icon name="coin" />
                        </span>
                        <span>Harga ramah kantong</span>
                    </li>
                </ul>
            </div>

            <div class="home-hero__visual">
                <div class="home-hero__art-frame">
                    <img src="{{ asset('images/food.png') }}"
                         alt="Ilustrasi pixel-art makanan dan minuman SiswaMart"
                         width="1024"
                         height="1024"
                         fetchpriority="high">
                </div>
                <span class="home-hero__sticker home-hero__sticker--rating">
                    @if((float) $heroRating > 0)
                        <x-public.home-icon name="star" />
                        {{ number_format($heroRating, 1) }} Rating
                    @else
                        Lihat ulasan produk
                    @endif
                </span>
                <span class="home-hero__sticker home-hero__sticker--price">
                    @if($heroPrice !== null)
                        Mulai Rp {{ number_format($heroPrice, 0, ',', '.') }}
                    @else
                        Cek harga di katalog
                    @endif
                </span>
            </div>
        </section>

        @if($kategoriList->isNotEmpty())
            <div class="home-marquee" aria-label="Kategori produk SiswaMart">
                <div class="home-marquee__track">
                    @foreach(range(1, 2) as $copy)
                        <div class="home-marquee__group" @if($copy === 2) aria-hidden="true" @endif>
                            @foreach($kategoriList as $kat)
                                <span class="home-marquee__item" style="--marquee-color: {{ $categoryColors[$kat->slug] ?? '#FFF6E5' }}">
                                    <span class="home-marquee__icon" aria-hidden="true"><x-public.home-icon :name="$categoryIcons[$kat->slug] ?? 'burger'" /></span>
                                    <span>{{ mb_strtoupper($kat->nama) }}</span>
                                </span>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($kategoriList->isNotEmpty())
            <section id="home-categories" class="home-container home-section" aria-labelledby="home-category-title">
                <div class="home-section__heading">
                    <h2 id="home-category-title" class="home-section__title">Pilih kategorimu</h2>
                    <a href="{{ route('public.kategori.index') }}" class="home-text-link">Lihat semua <span aria-hidden="true">?</span></a>
                </div>

                <div class="home-category-grid">
                    @foreach($kategoriList->take(4) as $kat)
                        @php
                            $icon = $categoryIcons[$kat->slug] ?? 'burger';
                            $color = $categoryColors[$kat->slug] ?? '#FFF6E5';
                        @endphp
                        <a href="{{ route('public.kategori.show', $kat->slug) }}" class="home-category-card" style="--category-color: {{ $color }}">
                            <span class="home-category-card__icon" aria-hidden="true" style="--icon-fill: {{ $color }}">
                                <x-public.home-icon :name="$icon" />
                            </span>
                            <span class="home-category-card__name">{{ $kat->nama }}</span>
                            <span class="home-category-card__arrow" aria-hidden="true"><x-public.home-icon name="arrow-right" /></span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <section id="home-products" class="home-container home-section" aria-labelledby="home-products-title">
            <div class="home-section__heading">
                <h2 id="home-products-title" class="home-section__title">Yang lagi populer.</h2>
                <a href="{{ route('public.produk.index') }}" class="home-button home-button--small">Lihat semua produk</a>
            </div>

            @if($produkRekomendasi->isNotEmpty())
                <div class="home-products-grid">
                    @foreach($produkRekomendasi->take(4) as $item)
                        <x-public.product-card :produk="$item" homepage />
                    @endforeach
                </div>
            @else
                <p class="home-products-empty">Belum ada produk untuk ditampilkan saat ini.</p>
            @endif
        </section>

        <section id="home-how-to-order" class="home-container home-section" aria-labelledby="home-how-to-title">
            <div class="home-how-to">
                <div class="home-how-to__intro">
                    <div>
                        <p class="home-how-to__eyebrow">Belanja langsung dari pembuatnya</p>
                        <h2 id="home-how-to-title" class="home-how-to__title">Cara memesan.<br>Temukan produk siswa.</h2>
                        <p class="home-how-to__description">Pilih produk, lalu atur pesanan langsung dengan penjual melalui WhatsApp.</p>
                    </div>
                    <a href="{{ route('public.cara_pemesanan') }}" class="home-button home-button--light">Lihat caranya</a>
                </div>

                <ol class="home-how-to__steps">
                    <li class="home-how-to__step">
                        <span class="home-how-to__step-icon" aria-hidden="true">
                            <x-public.home-icon name="search" />
                        </span>
                        <span class="home-how-to__number">01</span>
                        <span class="home-how-to__step-title">Pilih Produk</span>
                    </li>
                    <li class="home-how-to__step">
                        <span class="home-how-to__step-icon" aria-hidden="true">
                            <x-public.home-icon name="star" />
                        </span>
                        <span class="home-how-to__number">02</span>
                        <span class="home-how-to__step-title">Baca Ulasan</span>
                    </li>
                    <li class="home-how-to__step">
                        <span class="home-how-to__step-icon" aria-hidden="true">
                            <x-public.home-icon name="brand-whatsapp" />
                        </span>
                        <span class="home-how-to__number">03</span>
                        <span class="home-how-to__step-title">Hubungi Penjual</span>
                    </li>
                    <li class="home-how-to__step">
                        <span class="home-how-to__step-icon" aria-hidden="true">
                            <x-public.home-icon name="circle-check" />
                        </span>
                        <span class="home-how-to__number">04</span>
                        <span class="home-how-to__step-title">Atur Langsung</span>
                    </li>
                </ol>
            </div>
        </section>
    </div>
@endsection

