<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BadWord extends Model
{
    use HasFactory;

    protected $table = 'bad_words';

    protected $fillable = [
        'word',
        'language',
        'severity',
        'category',
        'type',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
