<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'book_id',
    'based_on_book_id',
    'reason',
    'score',
    'is_seen',
])]
class Recommendation extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'is_seen' => 'boolean',
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

    public function basedOnBook(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'based_on_book_id');
    }
}
