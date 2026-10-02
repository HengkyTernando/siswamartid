<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ulasan;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UlasanController extends Controller
{
    /**
     * Daftar ulasan pending.
     */
    public function index(): View
    {
        $ulasan = Ulasan::with('produk')
            ->orderByRaw("CASE WHEN status = 'pending' THEN 1 ELSE 2 END")
            ->latest()
            ->paginate(15);

        return view('admin.ulasan.index', compact('ulasan'));
    }

    /**
     * Approve ulasan.
     */
    public function approve(Ulasan $ulasan): RedirectResponse
    {
        $ulasan->update(['status' => 'disetujui']);
        return back()->with('success', 'Ulasan berhasil disetujui.');
    }

    /**
     * Reject ulasan.
     */
    public function reject(Ulasan $ulasan): RedirectResponse
    {
        $ulasan->update(['status' => 'ditolak']);
        return back()->with('success', 'Ulasan berhasil ditolak.');
    }
}
