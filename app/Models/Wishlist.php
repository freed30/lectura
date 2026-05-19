<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'book_id', 'note', 'priority'])]
class Wishlist extends Model
{
    use HasFactory;

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
