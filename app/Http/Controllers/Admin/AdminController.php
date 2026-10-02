<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Tampilkan daftar seluruh Admin.
     */
    public function index(): View
    {
        $admin = User::where('role', 'admin')
            ->latest()
            ->paginate(15);

        return view('admin.admin.index', compact('admin'));
    }

    /**
     * Tampilkan form tambah Admin.
     */
    public function create(): View
    {
        return view('admin.admin.create');
    }

    /**
     * Simpan akun Admin baru.
     *
     * Password otomatis di-hash menggunakan Bcrypt.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],

            // Password otomatis menjadi Bcrypt
            'password' => Hash::make($validated['password']),

            'role' => 'admin',
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.admin.index')
            ->with('success', 'Akun admin berhasil dibuat.');
    }

    /**
     * Tampilkan form edit Admin.
     */
    public function edit(User $admin): View
    {
        abort_unless($admin->role === 'admin', 404);

        return view('admin.admin.edit', compact('admin'));
    }

    /**
     * Perbarui akun Admin.
     *
     * Password hanya diubah jika diisi.
     */
    public function update(Request $request, User $admin): RedirectResponse
    {
        abort_unless($admin->role === 'admin', 404);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($admin->id),
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        // Jangan mengubah password jika field kosong
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $admin->update($data);

        return redirect()
            ->route('admin.admin.index')
            ->with('success', 'Akun admin berhasil diperbarui.');
    }

    /**
     * Aktifkan/nonaktifkan Admin.
     */
    public function toggleStatus(User $admin): RedirectResponse
    {
        abort_unless($admin->role === 'admin', 404);

        $admin->update([
            'is_active' => ! $admin->is_active,
        ]);

        $status = $admin->is_active
            ? 'diaktifkan'
            : 'dinonaktifkan';

        return redirect()
            ->route('admin.admin.index')
            ->with('success', "Akun admin berhasil {$status}.");
    }

    /**
     * Hapus akun Admin.
     *
     * Mencegah admin menghapus dirinya sendiri.
     */
    public function destroy(User $admin): RedirectResponse
    {
        abort_unless($admin->role === 'admin', 404);

        if ($admin->id === auth()->id()) {
            return redirect()
                ->route('admin.admin.index')
                ->with('error', 'Anda tidak dapat menghapus akun admin yang sedang digunakan.');
        }

        $admin->delete();

        return redirect()
            ->route('admin.admin.index')
            ->with('success', 'Akun admin berhasil dihapus.');
    }
}