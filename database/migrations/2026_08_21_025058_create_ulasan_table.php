<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel ulasan dari Pengunjung (Guest), tanpa perlu login.
     */
    public function up(): void
    {
        Schema::create('ulasan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk')->onDelete('cascade');
            $table->string('guest_username', 100);
            $table->tinyInteger('rating')->unsigned();
            $table->text('komentar')->nullable();
            $table->text('original_komentar')->nullable();
            $table->boolean('is_censored')->default(false);
            $table->string('status', 30)->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ulasan');
    }
};
