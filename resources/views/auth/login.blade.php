<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-xl font-bold text-[var(--text-main)] mb-2">Masuk ke Akunmu</h2>
        <p class="text-sm text-[var(--text-muted)]">Masukkan email dan password untuk masuk ke SiswaMart.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-sm font-medium text-green-600" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-semibold text-[var(--text-main)] mb-1.5">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                   class="form-input" placeholder="contoh@sekolah.sch.id">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-[#ea4335] text-xs font-medium" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-sm font-semibold text-[var(--text-main)]">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs font-semibold text-[var(--primary-color)] hover:text-[var(--primary-hover)] hover:underline">
                        Lupa password?
                    </a>
                @endif
            </div>
            
            <input id="password" type="password" name="password" required autocomplete="current-password" 
                   class="form-input" placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-[#ea4335] text-xs font-medium" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <div class="relative flex items-center justify-center w-5 h-5 mr-3">
                    <input id="remember_me" type="checkbox" name="remember" class="peer appearance-none w-5 h-5 border-2 border-[var(--border-color)] rounded-md checked:bg-[var(--primary-color)] checked:border-[var(--primary-color)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--focus-ring)] transition-all cursor-pointer">
                    <svg class="absolute w-3.5 h-3.5 text-white pointer-events-none opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium text-[var(--text-muted)] group-hover:text-[var(--text-main)] transition-colors">Ingat saya di perangkat ini</span>
            </label>
        </div>

        <div class="pt-2">
            <button type="submit" class="btn-primary w-full shadow-md shadow-blue-500/20">
                Masuk
            </button>
        </div>
    </form>
</x-guest-layout>
