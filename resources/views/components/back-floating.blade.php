@props(['fallback' => route('public.home')])

<a
    href="{{ $fallback }}"
    class="floating-back-button"
    aria-label="Kembali ke halaman sebelumnya"
    title="Kembali"
    onclick="if (window.history.length > 1) { event.preventDefault(); window.history.back(); }"
>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="m15 18-6-6 6-6" />
        <path d="M9 12h12" />
    </svg>
</a>
