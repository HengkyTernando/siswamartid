<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ulasan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KomentarController extends Controller
{
    public function index(): View
    {
        $komentarList = Ulasan::with(['produk.penjual'])
            ->latest()
            ->paginate(15);

        return view('admin.komentar.index', compact('komentarList'));
    }

    public function destroy(Ulasan $ulasan): RedirectResponse
    {
        $ulasan->delete();

        return redirect()
            ->route('admin.komentar.index')
            ->with('success', 'Ulasan berhasil dihapus.');
    }
}
