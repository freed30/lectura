<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'theme',
    'font_size',
    'line_spacing',
    'page_flip_enabled',
    'immersive_mode_default',
    'search_history',
])]
class UserPreference extends Model
{
    use HasFactory;

    protected $table = 'user_preferences';

    protected function casts(): array
    {
        return [
            'page_flip_enabled' => 'boolean',
            'immersive_mode_default' => 'boolean',
            'search_history' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
