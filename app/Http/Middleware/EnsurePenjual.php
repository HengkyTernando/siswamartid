<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePenjual
{
    /**
     * Hanya user dengan role 'penjual' yang dapat mengakses.
     * Guest diarahkan ke login, user dengan role lain mendapat 403.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role !== 'penjual') {
            abort(403, 'Akses ditolak. Halaman ini khusus untuk Penjual.');
        }

        if (! Auth::user()->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Akun toko Anda sedang dinonaktifkan oleh sekolah. Silakan hubungi admin.',
            ]);
        }

        return $next($request);
    }
}
