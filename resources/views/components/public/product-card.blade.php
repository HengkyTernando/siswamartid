{{--
    Product Card — Public Catalog
    Storefront-style: foto dominan, typography bold, organic playful vibe.
--}}
@props(['produk'])

<a href="{{ route('public.produk.show', $produk->slug) }}"
   id="produk-card-{{ $produk->id }}"
   class="product-card group flex flex-col h-full focus:outline-none bg-[var(--color-surface)] border-2 border-[var(--color-border)] shadow-solid-sm hover:shadow-solid-md hover:-translate-y-1 transition-all duration-200 rounded-[24px] overflow-hidden"
   aria-label="Lihat detail {{ $produk->nama }}">

    {{-- ── Foto Produk ──────────────────────────────────────── --}}
    <div class="relative w-full aspect-square overflow-hidden bg-white border-b-2 border-[var(--color-border)]">
        @if($produk->foto_utama)
            <img src="{{ asset('storage/' . $produk->foto_utama) }}"
                 alt="{{ $produk->nama }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 ease-out"
                 loading="lazy">
        @else
            <div class="img-placeholder w-full h-full gap-3 bg-[var(--color-bg)] flex flex-col items-center justify-center">
                @php
                    $slug  = $produk->kategori->slug ?? '';
                    $catIcons = [
                        'makanan' => 'food.svg',
                        'minuman' => 'drink.svg',
                        'snack' => 'snack.svg',
                        'dessert' => 'dessert.svg',
                    ];
                    $iconFile = $catIcons[$slug] ?? 'bag.svg';
                @endphp
                <div class="w-16 h-16 opacity-40 grayscale">
                    <img src="{{ asset('images/icons/' . $iconFile) }}" alt="Placeholder" class="w-full h-full object-contain">
                </div>
            </div>
        @endif

        {{-- Status / Promo Badges (Top Left) --}}
        <div class="absolute top-3 left-3 flex flex-col gap-1.5 z-10">
            @php
                $badge = null;
                $labelPromosi = $produk->label_promosi ? strtoupper(trim($produk->label_promosi)) : '';
                $isLabelPromoPerformance = in_array($labelPromosi, ['BEST SELLER', 'TERLARIS', 'HITS', 'FAVORIT', 'BARU', 'PRE-ORDER', 'PRE ORDER']);

                if ($produk->stok === 'habis') {
                    $badge = ['text' => 'HABIS', 'bg' => 'bg-[var(--color-danger)]', 'textCol' => 'text-white'];
                } elseif ($produk->stok === 'po') {
                    $badge = ['text' => 'PRE-ORDER', 'bg' => 'bg-[var(--color-warning)]', 'textCol' => 'text-[var(--color-text)]'];
                } elseif ($produk->views >= 500) {
                    $badge = ['text' => 'BEST SELLER', 'bg' => 'bg-[var(--color-danger)]', 'textCol' => 'text-white'];
                } elseif ($produk->views >= 100) {
                    $badge = ['text' => 'HITS', 'bg' => 'bg-[var(--color-warning)]', 'textCol' => 'text-[var(--color-text)]'];
                } elseif ($produk->views >= 50) {
                    $badge = ['text' => 'FAVORIT', 'bg' => 'bg-[var(--color-primary)]', 'textCol' => 'text-white'];
                } elseif ($produk->created_at && $produk->created_at->diffInDays(now()) <= 1) {
                    $badge = ['text' => 'BARU', 'bg' => 'bg-[var(--color-success)]', 'textCol' => 'text-white'];
                } elseif ($produk->label_promosi && !$isLabelPromoPerformance) {
                    $badge = ['text' => $produk->label_promosi, 'bg' => 'bg-white', 'textCol' => 'text-[var(--color-text)]'];
                }
            @endphp

            @if($badge)
                <span class="inline-block {{ $badge['bg'] }} {{ $badge['textCol'] }} border-2 border-[var(--color-border)] text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase tracking-wider shadow-solid-sm transform -rotate-1">
                    {{ $badge['text'] }}
                </span>
            @endif
            @if($produk->stok === 'ready')
                <span class="inline-block bg-[#BBF7D0] text-[#166534] border border-[#16A34A] text-[9px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider">Tersedia</span>
            @endif
        </div>

    </div>

    {{-- ── Info Produk ───────────────────────────────────────── --}}
    <div class="p-4 sm:p-5 flex flex-col flex-1 justify-between bg-[var(--color-surface)]">
        <div>
            {{-- Nama Produk --}}
            <h3 class="text-base sm:text-lg font-black text-[var(--color-text)] leading-snug line-clamp-2 mb-1.5 group-hover:text-[var(--color-primary)] transition-colors duration-150">
                {{ $produk->nama }}
            </h3>

            {{-- Toko Penjual --}}
            <div class="flex items-center gap-1.5 text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wider mb-2">
                <svg class="w-3.5 h-3.5 text-[var(--color-primary)] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span class="truncate">{{ $produk->penjual->nama_toko ?? '-' }}</span>
            </div>
            @if($produk->kategori)
                <p class="mb-2 truncate text-[10px] font-bold text-[var(--color-text-muted)]">{{ $produk->kategori->nama }}</p>
            @endif
        </div>

        <div>
            {{-- Dashed Divider --}}
            <div class="pt-2 border-t-2 border-dashed border-[var(--color-border)]/20 mb-3"></div>

            {{-- Harga & Stat/Rating --}}
            <div class="flex items-center justify-between gap-2">
                {{-- Harga --}}
                <span class="text-base sm:text-lg font-black text-[var(--color-primary)] tracking-tight">
                    Rp {{ number_format($produk->harga, 0, ',', '.') }}
                </span>

                {{-- Rating / Views Badge --}}
                <div class="flex items-center gap-1 shrink-0">
                    @php
                        $avgRating = $produk->ulasan_avg_rating ?? $produk->average_rating ?? 0;
                        $revCount  = $produk->ulasan_count ?? $produk->review_count ?? 0;
                    @endphp
                    @if($revCount > 0)
                        <div class="flex items-center gap-1 bg-[#FFFDED] border border-[var(--color-warning)] px-2 py-0.5 rounded-full text-[11px] font-black text-[var(--color-text)]">
                            <svg class="w-3 h-3 text-[var(--color-primary)] fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span>{{ number_format($avgRating, 1) }}</span>
                        </div>
                    @else
                        <div class="flex items-center gap-1 text-[11px] font-bold text-[var(--color-text-muted)] bg-[var(--color-bg)] px-2 py-0.5 rounded-full border border-[var(--color-border)]/10">
                            <svg class="w-3 h-3 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span>{{ number_format($produk->views, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</a>
