<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'book_id', 'rating', 'review_text', 'is_visible'])]
class Review extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
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
