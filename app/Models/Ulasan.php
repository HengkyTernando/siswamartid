<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ulasan extends Model
{
    use HasFactory;

    /**
     * Nama tabel eksplisit — mencegah Laravel auto-pluralize ke 'ulasans'.
     */
    protected $table = 'ulasan';

    /**
     * Kolom yang boleh diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'produk_id',
        'guest_username',
        'rating',
        'komentar',
        'original_komentar',
        'display_content',
        'is_censored',
        'status',
        'moderation_status',
        'detected_words',
        'moderation_reason',
        'moderated_at',
        'moderated_by',
    ];

    /**
     * Cast tipe data kolom.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating'         => 'integer',
            'is_censored'    => 'boolean',
            'detected_words' => 'array',
            'moderated_at'   => 'datetime',
        ];
    }

    /**
     * Accessor untuk teks yang ditampilkan ke publik.
     * Menggunakan display_content jika ada (versi sensor admin), jika tidak menggunakan komentar asli.
     */
    public function getPublicContentAttribute(): ?string
    {
        return $this->display_content ?? $this->komentar;
    }

    // ─── Scope ────────────────────────────────────────────────────────────────

    /**
     * Scope ulasan yang dapat ditampilkan di publik.
     * HANYA komentar dengan moderation_status = 'approved'.
     */
    public function scopePublicVisible(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where(function (Builder $approved) {
                $approved->where('moderation_status', 'approved')
                    ->whereNotIn('status', ['disembunyikan', 'ditolak', 'dihapus', 'rejected']);
            })
              ->orWhere(function (Builder $sub) {
                  // Fallback data lama jika moderation_status belum terisi
                  $sub->whereNull('moderation_status')
                      ->whereIn('status', ['aktif', 'disetujui', 'disensor']);
              });
        });
    }

    // ─── Relasi ───────────────────────────────────────────────────────────────

    /**
     * Ulasan dimiliki oleh satu Produk.
     */
    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }

    /**
     * Admin yang memoderasi ulasan ini.
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /**
     * Log audit moderasi.
     */
    public function moderationLogs(): HasMany
    {
        return $this->hasMany(CommentModerationLog::class, 'ulasan_id');
    }
}
