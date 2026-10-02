<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Hanya user dengan role 'admin' yang dapat mengakses.
     * Guest diarahkan ke login, user dengan role lain mendapat 403.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Halaman ini khusus untuk Admin.');
        }

        return $next($request);
    }
}
