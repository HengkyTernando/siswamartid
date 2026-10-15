@props([
    'kategoriList',
    'selectedKategori' => null,
])

@php
    $categoryIcons = ['makanan' => 'burger', 'makanan-berat' => 'burger', 'minuman' => 'glass-full', 'snack' => 'cookie', 'dessert' => 'cake'];
    $selectedIcon = $selectedKategori
        ? ($categoryIcons[$selectedKategori->slug] ?? null)
        : null;
@endphp

@once
    <style>
        .category-select-option {
            transition: background-color 160ms ease, border-color 160ms ease, color 160ms ease, box-shadow 160ms ease, transform 160ms ease;
        }

        .category-select-option > span:first-child {
            color: var(--color-text);
        }

        .category-select-option:hover {
            border-color: var(--color-border);
            background-color: var(--color-primary);
            color: #fff;
            box-shadow: 4px 4px 0 var(--color-border);
            transform: translateY(-1px);
        }
    </style>
@endonce

{{-- Mobile: custom dropdown menu --}}
<div
    class="relative z-30 lg:hidden"
    x-data="{ categoryOpen: false, searchOpen: false, priceOpen: false, sortOpen: false }"
    @click.outside="categoryOpen = false; searchOpen = false; priceOpen = false; sortOpen = false"
    @keydown.escape.window="categoryOpen = false; searchOpen = false; priceOpen = false; sortOpen = false"
>
    <div class="flex flex-wrap items-center gap-2">
        <div class="relative min-w-0 flex-1">
            <button
                type="button"
                class="flex min-h-14 w-full items-center gap-3 rounded-full border-2 border-[var(--color-border)] bg-[var(--color-surface)] px-3 sm:px-4 py-2.5 text-left shadow-solid-sm"
                aria-controls="mobile-category-options"
                :aria-expanded="categoryOpen.toString()"
                @click="categoryOpen = !categoryOpen; searchOpen = false; priceOpen = false; sortOpen = false"
            >
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border-2 border-[var(--color-border)] bg-white p-2">
                    @if($selectedIcon)
                        <x-public.home-icon :name="$selectedIcon" class="h-full w-full" aria-hidden="true" />
                    @else
                        <x-public.home-icon name="package" class="h-full w-full" aria-hidden="true" />
                    @endif
                </span>
                <span class="min-w-0 flex-1 truncate text-sm sm:text-base font-black text-[var(--color-text)]">{{ $selectedKategori?->nama ?? 'Semua Produk' }}</span>
                <svg class="h-5 w-5 shrink-0 text-[var(--color-primary)] transition-transform" :class="categoryOpen ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path d="m5 7.5 5 5 5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>

            <nav
                id="mobile-category-options"
                x-show="categoryOpen"
                x-cloak
                x-transition.origin.top.left
                class="absolute left-0 right-0 top-full z-50 mt-3 max-h-[70vh] overscroll-contain overflow-y-auto rounded-3xl border-2 border-[var(--color-border)] bg-[var(--color-surface)] p-3 shadow-[0_12px_28px_rgba(36,26,20,0.18)]"
                aria-label="Pilih kategori produk"
            >
                <a
                    href="{{ route('public.kategori.index') }}"
                    @class([
                        'category-select-option flex min-h-14 items-center gap-3 rounded-full border-2 px-4 py-3 text-sm font-bold transition-colors',
                        'border-[var(--color-border)] bg-[var(--color-primary)] text-white' => $selectedKategori === null,
                        'border-transparent text-[var(--color-text)] hover:border-[var(--color-border)] hover:bg-[var(--color-primary)] hover:text-white' => $selectedKategori !== null,
                    ])
                    @if($selectedKategori === null) aria-current="page" @endif
                >
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border-2 border-[var(--color-border)] bg-white p-1.5">
                        <x-public.home-icon name="package" class="h-full w-full" aria-hidden="true" />
                    </span>
                    <span class="min-w-0 flex-1">Semua Produk</span>
                </a>

                @foreach($kategoriList as $kat)
                    @if($kat->produk_count > 0)
                        @php
                            $icon = $categoryIcons[$kat->slug] ?? 'package';
                            $isSelected = $selectedKategori?->id === $kat->id;
                        @endphp
                        <a
                            href="{{ route('public.kategori.show', $kat->slug) }}"
                            @class([
                                'category-select-option mt-1.5 flex min-h-14 items-center gap-3 rounded-full border-2 px-4 py-3 text-sm font-bold transition-colors',
                                'border-[var(--color-border)] bg-[var(--color-primary)] text-white' => $isSelected,
                                'border-transparent text-[var(--color-text)] hover:border-[var(--color-border)] hover:bg-[var(--color-primary)] hover:text-white' => ! $isSelected,
                            ])
                            @if($isSelected) aria-current="page" @endif
                        >
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border-2 border-[var(--color-border)] bg-white p-1.5">
                                <x-public.home-icon :name="$icon" class="h-full w-full" aria-hidden="true" />
                            </span>
                            <span class="min-w-0 flex-1">{{ $kat->nama }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>
        </div>

        <div class="ml-auto flex shrink-0 items-center gap-2">
            <button
                type="button"
                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full border-2 border-[var(--color-border)] bg-[var(--color-surface)] text-[var(--color-primary)] shadow-solid-sm transition-transform hover:-translate-y-0.5"
                aria-label="Cari produk"
                aria-controls="mobile-search-panel"
                :aria-expanded="searchOpen.toString()"
                @click="searchOpen = !searchOpen; categoryOpen = false; priceOpen = false; sortOpen = false"
            >
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor" stroke-width="2.2" />
                    <path d="m16 16 4.5 4.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" />
                </svg>
            </button>

            <div class="relative z-40 shrink-0">
                <button
                    type="button"
                    class="flex h-12 w-12 items-center justify-center rounded-full border-2 border-[var(--color-border)] bg-[var(--color-surface)] text-[var(--color-primary)] shadow-solid-sm"
                    aria-label="Urutkan produk"
                    aria-controls="mobile-sort-options"
                    :aria-expanded="sortOpen.toString()"
                    @click="sortOpen = !sortOpen; categoryOpen = false; searchOpen = false; priceOpen = false"
                >
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 5h16l-6.2 7v5.5l-3.6 1.8V12L4 5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
                <nav
                    id="mobile-sort-options"
                    x-show="sortOpen"
                    x-cloak
                    x-transition.origin.top.right
                    class="absolute right-0 top-full z-50 mt-2 w-48 rounded-2xl border-2 border-[var(--color-border)] bg-[var(--color-surface)] p-2 shadow-[0_8px_20px_rgba(36,26,20,0.14)]"
                    aria-label="Urutkan produk"
                >
                    <button type="button" role="menuitemradio" :aria-checked="(sort === 'terbaru').toString()" :style="sort === 'terbaru' ? 'background-color:var(--color-primary);border-color:var(--color-border);color:#fff' : ''" @click="sort = 'terbaru'; sortOpen = false; submitForm()" class="mb-1 w-full rounded-xl border-2 border-transparent px-3 py-2.5 text-left text-sm font-bold text-[var(--color-text)] transition-colors hover:border-[var(--color-border)] hover:bg-[var(--color-primary)] hover:text-white">Terbaru</button>
                    <button type="button" role="menuitemradio" :aria-checked="(sort === 'terpopuler').toString()" :style="sort === 'terpopuler' ? 'background-color:var(--color-primary);border-color:var(--color-border);color:#fff' : ''" @click="sort = 'terpopuler'; sortOpen = false; submitForm()" class="mb-1 w-full rounded-xl border-2 border-transparent px-3 py-2.5 text-left text-sm font-bold text-[var(--color-text)] transition-colors hover:border-[var(--color-border)] hover:bg-[var(--color-primary)] hover:text-white">Terpopuler</button>
                    <button type="button" role="menuitemradio" :aria-checked="(sort === 'termurah').toString()" :style="sort === 'termurah' ? 'background-color:var(--color-primary);border-color:var(--color-border);color:#fff' : ''" @click="sort = 'termurah'; sortOpen = false; submitForm()" class="mb-1 w-full rounded-xl border-2 border-transparent px-3 py-2.5 text-left text-sm font-bold text-[var(--color-text)] transition-colors hover:border-[var(--color-border)] hover:bg-[var(--color-primary)] hover:text-white">Termurah</button>
                    <button type="button" role="menuitemradio" :aria-checked="(sort === 'termahal').toString()" :style="sort === 'termahal' ? 'background-color:var(--color-primary);border-color:var(--color-border);color:#fff' : ''" @click="sort = 'termahal'; sortOpen = false; submitForm()" class="w-full rounded-xl border-2 border-transparent px-3 py-2.5 text-left text-sm font-bold text-[var(--color-text)] transition-colors hover:border-[var(--color-border)] hover:bg-[var(--color-primary)] hover:text-white">Termahal</button>
                </nav>
            </div>

            <button
                type="button"
                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full border-2 border-[var(--color-border)] bg-[var(--color-primary)] text-white shadow-solid-sm transition-transform hover:-translate-y-0.5"
                aria-label="Atur harga"
                aria-controls="mobile-price-panel"
                :aria-expanded="priceOpen.toString()"
                @click="priceOpen = !priceOpen; categoryOpen = false; searchOpen = false; sortOpen = false"
            >
                <x-public.home-icon name="filter" class="h-6 w-6" aria-hidden="true" />
            </button>
        </div>
    </div>

    <form
        id="mobile-search-panel"
        x-show="searchOpen"
        x-cloak
        x-transition.origin.top.right
        @submit.prevent="submitForm()"
        class="mt-3 flex items-center gap-2 rounded-3xl border-2 border-[var(--color-border)] bg-[var(--color-surface)] p-3 shadow-[0_8px_20px_rgba(36,26,20,0.14)]"
    >
        <input
            type="search"
            x-model="q"
            aria-label="Cari produk"
            placeholder="Cari produk..."
            class="min-w-0 flex-1 rounded-full border-2 border-[var(--color-border)] bg-white px-4 py-3 text-sm font-semibold text-[var(--color-text)] outline-none focus:border-[var(--color-primary)]"
        >
        <button type="submit" class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full border-2 border-[var(--color-border)] bg-[var(--color-primary)] text-white" aria-label="Terapkan pencarian">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor" stroke-width="2.2" />
                <path d="m16 16 4.5 4.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" />
            </svg>
        </button>
    </form>

    <section
        id="mobile-price-panel"
        x-show="priceOpen"
        x-cloak
        x-transition.origin.top.right
        class="mt-3 rounded-3xl border-2 border-[var(--color-border)] bg-[var(--color-surface)] p-4 shadow-[0_8px_20px_rgba(36,26,20,0.14)]"
        aria-label="Atur harga produk"
    >
        <h3 class="mb-3 text-base font-black uppercase tracking-wide text-[var(--color-text)]">Atur Harga</h3>
        <div class="grid grid-cols-2 gap-3">
            <label class="text-xs font-bold uppercase tracking-wider text-[var(--color-text-muted)]">
                Min (Rp)
                <input type="text" inputmode="numeric" x-model="min_price" @input="min_price = min_price.toString().replace(/[^0-9]/g, '')" @change="validateInput()" placeholder="0" class="mt-1 w-full rounded-xl border-2 border-[var(--color-border)] bg-white px-3 py-2.5 text-sm font-bold text-[var(--color-text)] outline-none focus:border-[var(--color-primary)]">
            </label>
            <label class="text-xs font-bold uppercase tracking-wider text-[var(--color-text-muted)]">
                Max (Rp)
                <input type="text" inputmode="numeric" x-model="max_price" @input="max_price = max_price.toString().replace(/[^0-9]/g, '')" @change="validateInput()" placeholder="50000" class="mt-1 w-full rounded-xl border-2 border-[var(--color-border)] bg-white px-3 py-2.5 text-sm font-bold text-[var(--color-text)] outline-none focus:border-[var(--color-primary)]">
            </label>
        </div>
        <div class="mt-4 flex gap-2">
            <button type="button" @click="resetFilter()" class="flex-1 rounded-full border-2 border-[var(--color-border)] bg-white px-3 py-2.5 text-xs font-bold uppercase tracking-wide text-[var(--color-text)]">Reset</button>
            <button type="button" @click="submitForm()" class="btn-primary flex-1 bg-[var(--color-primary)] px-3 py-2.5 text-xs uppercase tracking-wide text-white">Terapkan</button>
        </div>
    </section>
</div>

{{-- Desktop: category navigation in the sidebar --}}
<div class="hidden lg:block">
    <h2 class="mb-5 text-xl font-black uppercase tracking-wider text-[var(--color-text)]">Kategori</h2>
    <nav class="space-y-2" aria-label="Kategori produk">
        <a
            href="{{ route('public.kategori.index') }}"
            @class([
                'category-select-option flex items-center gap-3 rounded-full border-2 px-3 py-2.5 text-sm font-bold transition-all',
                'border-[var(--color-border)] bg-[var(--color-primary)] text-white shadow-solid-sm' => $selectedKategori === null,
                'border-transparent text-[var(--color-text)] hover:border-[var(--color-border)] hover:bg-[var(--color-primary)] hover:text-white' => $selectedKategori !== null,
            ])
        >
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border-2 border-[var(--color-border)] bg-white p-1.5">
                <x-public.home-icon name="package" class="h-full w-full" aria-hidden="true" />
            </span>
            <span class="min-w-0 flex-1 uppercase tracking-wider">Semua Produk</span>
        </a>

        @foreach($kategoriList as $kat)
            @if($kat->produk_count > 0)
                @php
                    $icon = $categoryIcons[$kat->slug] ?? 'package';
                    $isSelected = $selectedKategori?->id === $kat->id;
                @endphp
                <a
                    href="{{ route('public.kategori.show', $kat->slug) }}"
                    @class([
                        'category-select-option flex items-center gap-3 rounded-full border-2 px-3 py-2.5 text-sm font-bold transition-all',
                        'border-[var(--color-border)] bg-[var(--color-primary)] text-white shadow-solid-sm' => $isSelected,
                        'border-transparent text-[var(--color-text)] hover:border-[var(--color-border)] hover:bg-[var(--color-primary)] hover:text-white' => ! $isSelected,
                    ])
                >
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border-2 border-[var(--color-border)] bg-white p-1.5">
                        <x-public.home-icon :name="$icon" class="h-full w-full" aria-hidden="true" />
                    </span>
                    <span class="min-w-0 flex-1 uppercase tracking-wider">{{ $kat->nama }}</span>
                </a>
            @endif
        @endforeach
    </nav>
</div>

