<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommentModerationLog extends Model
{
    use HasFactory;

    protected $table = 'comment_moderation_logs';

    protected $fillable = [
        'ulasan_id',
        'admin_id',
        'action',
        'original_status',
        'new_status',
    ];

    public function ulasan(): BelongsTo
    {
        return $this->belongsTo(Ulasan::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
