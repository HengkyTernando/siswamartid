<x-guest-layout>
    <div class="mb-5 text-center">
        <a href="{{ route('public.home') }}" class="auth-wordmark mb-2.5 inline-flex rounded-full px-4 py-1.5 font-display text-sm font-bold leading-none" aria-label="SiswaMart, kembali ke beranda">
            <span class="auth-wordmark__siswamart">Siswa</span><span class="auth-wordmark__mart">Mart</span>
        </a>
        <h1 class="font-display text-2xl font-bold text-[var(--text-main)]">Lupa password?</h1>
        <p class="mt-1 text-xs text-[var(--ink-soft)]">Masukkan email akunmu untuk menerima link reset password.</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div role="status" class="mb-4 flex items-start gap-2.5 rounded-xl border-2 border-[#166534] bg-[#f0fdf4] px-3.5 py-2.5 text-xs font-semibold text-[#166534] shadow-[2px_2px_0_var(--ink)]">
            <svg aria-hidden="true" class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="mb-1.5 block text-xs font-bold text-[var(--text-main)]">Email Terdaftar</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="form-input" placeholder="email@gmail.com">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs font-medium text-[#b42318]" />
        </div>

        <button type="submit" class="btn-primary w-full justify-center py-3 text-sm font-bold">
            Kirim Link Reset Password
        </button>
    </form>

    <div class="mt-5 text-center">
        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[var(--ink)] hover:text-[var(--brand-ink)] hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--ink)]">
            <svg aria-hidden="true" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Kembali ke halaman login
        </a>
    </div>
</x-guest-layout>
