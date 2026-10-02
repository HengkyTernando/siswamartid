@extends('layouts.public')

@section('title', $kategori->nama . ' | Katalog SiswaMart')
@section('meta_description', 'Jelajahi produk ' . strtolower($kategori->nama) . ' karya siswa di SiswaMart.')

@section('content')

<style>
.range-slider input[type="range"]::-webkit-slider-thumb {
    pointer-events: auto;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: var(--color-primary);
    border: 2px solid var(--color-border);
    cursor: pointer;
    -webkit-appearance: none;
}
.range-slider input[type="range"]::-moz-range-thumb {
    pointer-events: auto;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: var(--color-primary);
    border: 2px solid var(--color-border);
    cursor: pointer;
}
</style>

<div class="bg-[var(--color-bg)] min-h-screen pb-20 pt-8" x-data="{
    q: '{{ $search ?? '' }}',
    sort: '{{ $sort ?? 'terbaru' }}',
    min_price: '{{ $minPrice ?? '' }}',
    max_price: '{{ $maxPrice ?? '' }}',
    get sliderMin() {
        return this.min_price === '' ? 0 : parseInt(this.min_price);
    },
    set sliderMin(val) {
        this.min_price = val;
    },
    get sliderMax() {
        return this.max_price === '' ? 50000 : parseInt(this.max_price);
    },
    set sliderMax(val) {
        this.max_price = val;
    },
    validateInput() {
        let min = parseInt(this.min_price);
        let max = parseInt(this.max_price);
        if (isNaN(min) || min < 0) min = 0;
        if (isNaN(max) || max > 50000) max = 50000;
        if (min > max) {
            let tmp = min;
            min = max;
            max = tmp;
        }
        this.min_price = min;
        this.max_price = max;
    },
    submitForm() {
        this.validateInput();
        if (this.min_price == 0) this.min_price = '';
        if (this.max_price == 50000) this.max_price = '';
        $refs.mainForm.submit();
    },
    resetFilter() {
        this.min_price = '';
        this.max_price = '';
        this.submitForm();
    }
}">
    
    <form x-ref="mainForm" action="{{ route('public.kategori.show', $kategori->slug) }}" method="GET" class="hidden">
        <input type="hidden" name="q" x-model="q">
        <input type="hidden" name="sort" x-model="sort">
        <input type="hidden" name="min_price" x-model="min_price">
        <input type="hidden" name="max_price" x-model="max_price">
    </form>

    <div class="max-w-[1400px] mx-auto px-6 lg:px-[60px]">
        
        {{-- ─── Breadcrumb ────────────────────────────────────────────── --}}
        <nav class="flex items-center gap-2 text-sm text-[var(--color-text-muted)] font-bold mb-8 uppercase tracking-widest" aria-label="Breadcrumb">
            <a href="{{ route('public.home') }}" class="hover:text-[var(--color-primary)] transition-colors">BERANDA</a>
            <span class="text-[var(--color-border)]">/</span>
            <a href="{{ route('public.kategori.index') }}" class="hover:text-[var(--color-primary)] transition-colors">KATEGORI</a>
            <span class="text-[var(--color-border)]">/</span>
            <span class="text-[var(--color-text)] uppercase">{{ $kategori->nama }}</span>
        </nav>

        {{-- ─── Header Section ────────────────────────────────────────── --}}
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-12 border-b-4 border-[var(--color-border)] pb-8">
            <div>
                <h1 class="text-5xl font-black text-[var(--color-text)] mb-3 uppercase tracking-tighter">{{ $kategori->nama }}</h1>
                <p class="text-lg font-bold text-[var(--color-text-muted)]">Temukan {{ strtolower($kategori->nama) }} favorit buatan teman-temanmu.</p>
            </div>
            
            <div class="flex flex-col sm:flex-row items-center gap-6 w-full lg:w-auto">
                <div class="bg-white border-2 border-[var(--color-border)] px-4 py-2 rounded-full font-bold shadow-solid-sm text-sm">
                    {{ $produk->total() }} PRODUK
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <span class="text-sm font-bold text-[var(--color-text-muted)] uppercase tracking-wider">URUTKAN</span>
                    <select x-model="sort" @change="submitForm()"
                            class="w-full sm:w-auto pl-4 pr-10 py-3 text-sm font-bold uppercase tracking-wider border-2 border-[var(--color-border)] bg-white rounded-xl shadow-solid-sm focus:outline-none focus:ring-0 appearance-none cursor-pointer hover:-translate-y-0.5 transition-transform">
                        <option value="terbaru">Terbaru</option>
                        <option value="terpopuler">Terpopuler</option>
                        <option value="termurah">Termurah</option>
                        <option value="termahal">Termahal</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-10">

            {{-- ─── Sidebar (Desktop) ──────────────────────── --}}
            <aside class="w-full lg:w-[320px] flex-shrink-0">
                <div class="sticky top-24 space-y-8">
                    
                    {{-- Card Kategori --}}
                    <div class="bg-[var(--color-surface)] rounded-3xl border-2 border-[var(--color-border)] shadow-solid-md p-6">
                        <h2 class="text-xl font-black text-[var(--color-text)] mb-6 uppercase tracking-wider">Kategori</h2>
                        <ul class="space-y-3">
                            <li>
                                <a href="{{ route('public.kategori.index') }}"
                                   class="flex items-center justify-between px-4 py-3 rounded-2xl text-sm font-bold text-[var(--color-text)] bg-white border-2 border-[var(--color-border)] shadow-sm hover:shadow-solid-sm hover:-translate-y-1 transition-all group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center p-1.5 flex-shrink-0 border-2 border-[var(--color-border)]">
                                            <img src="{{ asset('images/icons/bag.svg') }}" alt="Semua Kategori" class="w-full h-full object-contain">
                                        </div>
                                        <span class="uppercase tracking-wider">Semua Kategori</span>
                                    </div>
                                    <span class="text-xs bg-[var(--color-bg)] px-2 py-1 rounded-full border-2 border-[var(--color-border)]">{{ $totalSemua }}</span>
                                </a>
                            </li>
                            @foreach($kategoriList as $kat)
                                @if($kat->produk_count > 0)
                                    @php
                                        $catData = [
                                            'makanan' => ['icon'=>'food.svg', 'bg'=>'bg-[#FFB084]', 'hover'=>'hover:bg-[#FFB084]'],
                                            'minuman' => ['icon'=>'drink.svg', 'bg'=>'bg-[#A3D9C9]', 'hover'=>'hover:bg-[#A3D9C9]'],
                                            'snack' => ['icon'=>'snack.svg', 'bg'=>'bg-[#FFD84D]', 'hover'=>'hover:bg-[#FFD84D]'],
                                            'dessert' => ['icon'=>'dessert.svg', 'bg'=>'bg-[#D4C4FB]', 'hover'=>'hover:bg-[#D4C4FB]'],
                                        ];
                                        $data = $catData[$kat->slug] ?? ['icon'=>'bag.svg', 'bg'=>'bg-[#FFF8E8]', 'hover'=>'hover:bg-[#FFF8E8]'];
                                        $isActive = $kat->id === $kategori->id;
                                    @endphp
                                    <li>
                                        <a href="{{ route('public.kategori.show', $kat->slug) }}"
                                           class="flex items-center justify-between px-4 py-3 rounded-2xl text-sm font-bold transition-all group 
                                           {{ $isActive ? 'bg-[var(--color-primary)] text-white border-2 border-[var(--color-border)] shadow-solid-sm' : 'text-[var(--color-text)] bg-white border-2 border-[var(--color-border)] shadow-sm hover:shadow-solid-sm hover:-translate-y-1 ' . $data['hover'] }}">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 {{ $isActive ? 'bg-white' : $data['bg'] }} rounded-full flex items-center justify-center p-1.5 flex-shrink-0 border-2 border-[var(--color-border)]">
                                                    <img src="{{ asset('images/icons/' . $data['icon']) }}" alt="{{ $kat->nama }}" class="w-full h-full object-contain">
                                                </div>
                                                <span class="uppercase tracking-wider">{{ $kat->nama }}</span>
                                            </div>
                                            <span class="text-xs {{ $isActive ? 'bg-white text-[var(--color-text)]' : 'bg-[var(--color-bg)] text-[var(--color-text)]' }} px-2 py-1 rounded-full border-2 border-[var(--color-border)]">{{ $kat->produk_count }}</span>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>

                    {{-- Card Filter Harga --}}
                    <div class="bg-[var(--color-surface)] rounded-3xl border-2 border-[var(--color-border)] shadow-solid-md p-6">
                        <h2 class="text-xl font-black text-[var(--color-text)] mb-6 uppercase tracking-wider">Atur Harga</h2>
                        
                        {{-- Range visualization --}}
                        <div class="mb-6">
                            <div class="flex justify-between text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider mb-4">
                                <span>Min</span>
                                <span>Max</span>
                            </div>
                            <div class="relative h-3 bg-white border-2 border-[var(--color-border)] rounded-full range-slider">
                                <div class="absolute top-0 h-full bg-[var(--color-primary)] border-y-2 border-[var(--color-border)]"
                                     x-bind:style="`left: ${(sliderMin / 50000) * 100}%; right: ${100 - (sliderMax / 50000) * 100}%;`"></div>
                                
                                <input type="range" min="0" max="50000" step="1000" 
                                       x-model="sliderMin" 
                                       @input="if(sliderMin > sliderMax) sliderMin = sliderMax"
                                       class="absolute w-full h-3 opacity-0 cursor-pointer pointer-events-none appearance-none z-20">
                                       
                                <input type="range" min="0" max="50000" step="1000" 
                                       x-model="sliderMax" 
                                       @input="if(sliderMax < sliderMin) sliderMax = sliderMin"
                                       class="absolute w-full h-3 opacity-0 cursor-pointer pointer-events-none appearance-none z-20">
                            </div>
                        </div>

                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex-1">
                                <label class="text-xs font-bold text-[var(--color-text-muted)] block mb-2 uppercase tracking-wider">Min (Rp)</label>
                                <input type="text" x-model="min_price" @input="min_price = min_price.toString().replace(/[^0-9]/g, '')" @change="validateInput()" placeholder="0" class="w-full px-3 py-2 text-sm font-bold border-2 border-[var(--color-border)] rounded-xl focus:outline-none focus:ring-0">
                            </div>
                            <div class="flex-1">
                                <label class="text-xs font-bold text-[var(--color-text-muted)] block mb-2 uppercase tracking-wider">Max (Rp)</label>
                                <input type="text" x-model="max_price" @input="max_price = max_price.toString().replace(/[^0-9]/g, '')" @change="validateInput()" placeholder="50000" class="w-full px-3 py-2 text-sm font-bold border-2 border-[var(--color-border)] rounded-xl focus:outline-none focus:ring-0">
                            </div>
                        </div>

                        <button @click="submitForm()" class="btn-primary w-full bg-[var(--color-primary)] text-white text-sm py-3 mb-3 hover:bg-[var(--color-primary-dark)]">
                            TAMPILKAN PRODUK
                        </button>
                        <button @click="resetFilter()" class="w-full text-center text-xs font-bold text-[var(--color-text-muted)] hover:text-[var(--color-text)] transition-colors uppercase tracking-wider">
                            Atur Ulang Filter
                        </button>
                    </div>

                </div>
            </aside>

            {{-- ─── Main Content ──────────────────────────────────────── --}}
            <main class="flex-1 min-w-0">
                
                {{-- Product Grid --}}
                @if($produk->isEmpty())
                    <div class="bg-[var(--color-surface)] border-2 border-dashed border-[var(--color-border)] rounded-[40px] p-12 lg:p-20 text-center">
                        <div class="text-[80px] mb-6 grayscale opacity-40">
                            🕵️
                        </div>
                        <h3 class="text-3xl font-black text-[var(--color-text)] mb-4 uppercase">Kosong Melompong!</h3>
                        <p class="text-lg font-bold text-[var(--color-text-muted)] mb-8 max-w-md mx-auto">
                            @if($search || $minPrice || $maxPrice)
                                Filter yang kamu pilih terlalu ketat. Coba ubah atau reset filter.
                            @else
                                Belum ada produk aktif yang tersedia di kategori ini.
                            @endif
                        </p>
                        <a href="{{ route('public.kategori.index') }}" class="btn-primary inline-flex px-8 py-3 bg-[var(--color-primary)] text-white hover:bg-[var(--color-primary-dark)]">
                            LIHAT SEMUA PRODUK
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-2 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($produk as $item)
                            <x-public.product-card :produk="$item" />
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-16">
                        {{ $produk->links() }}
                    </div>
                @endif

            </main>
        </div>
    </div>
</div>

@endsection
