<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'book_id',
    'current_page',
    'current_location',
    'total_pages',
    'progress_percent',
    'is_finished',
    'last_read_at',
    'completed_at',
])]
class ReadingProgress extends Model
{
    use HasFactory;

    protected $table = 'reading_progress';

    protected function casts(): array
    {
        return [
            'progress_percent' => 'decimal:2',
            'is_finished' => 'boolean',
            'last_read_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    // relation utilisateur-livre.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // relation utilisateur-livre.
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
