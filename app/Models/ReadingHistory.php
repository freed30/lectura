<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'book_id',
    'started_at',
    'last_opened_at',
    'last_progress_percent',
    'completed_at',
])]
class ReadingHistory extends Model
{
    use HasFactory;

    protected $table = 'reading_history';

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'last_opened_at' => 'datetime',
            'last_progress_percent' => 'decimal:2',
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
