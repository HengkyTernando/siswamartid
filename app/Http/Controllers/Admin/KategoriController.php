<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $kategoris = Kategori::withCount('produk')
            ->when($search, function ($query, $search) {
                return $query->where('nama', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.kategori.index', compact('kategoris', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:kategori,nama',
        ]);

        Kategori::create([
            'nama' => $request->nama,
            'slug' => Str::slug($request->nama),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Kategori $category)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:kategori,nama,' . $category->id,
            'slug' => 'required|string|max:100|unique:kategori,slug,' . $category->id,
        ]);

        $category->update([
            'nama' => $request->nama,
            'slug' => Str::slug($request->slug), // allow slug update
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Kategori $category)
    {
        if ($category->produk()->count() > 0) {
            return redirect()->route('admin.categories.index')->with('error', 'Kategori belum bisa dihapus karena masih dipakai produk.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
