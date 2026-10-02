<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class CartController extends ApiController
{
    private const MESSAGE = 'Fitur cart belum tersedia pada backend SiswaMart saat ini. Tidak ada tabel atau model cart yang dapat digunakan.';

    public function index() { return $this->error(self::MESSAGE, [], 501); }
    public function store(Request $request) { return $this->error(self::MESSAGE, [], 501); }
    public function update(Request $request, string $cart) { return $this->error(self::MESSAGE, [], 501); }
    public function destroy(string $cart) { return $this->error(self::MESSAGE, [], 501); }
}
