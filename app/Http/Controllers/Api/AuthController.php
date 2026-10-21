<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends ApiController
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();
        if (! $user || ! $user->is_active || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => ['Email atau password tidak valid.']]);
        }

        $token = $user->createToken('siswamart-mobile')->plainTextToken;

        return $this->success(['user' => $this->userData($user), 'token' => $token], 'Login berhasil');
    }

    public function register(Request $request)
    {
        return $this->error(
            'Pendaftaran mandiri akun penjual melalui API tidak diizinkan. Akun penjual dikelola secara terpusat oleh pihak sekolah.',
            [],
            403
        );
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();
        return $this->success(null, 'Logout berhasil');
    }

    public function user(Request $request)
    {
        return $this->success($this->userData($request->user()), 'Data user berhasil diambil');
    }

    private function userData(User $user): array
    {
        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'is_active' => $user->is_active,
        ];

        return $data;
    }
}
