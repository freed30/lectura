<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;

class BookReviewService
{
    public function storeForUser(Book $book, User $user, array $data): Review
    {
        $review = Review::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'book_id' => $book->id,
            ],
            [
                'rating' => (int) $data['rating'],
                'review_text' => $data['review_text'] ?? null,
                'is_visible' => true,
            ]
        );

        $this->refreshAverageRating($book);

        return $review;
    }

    public function refreshAverageRating(Book $book): void
    {
        // calcul moyenne des notes visibles pour affichage rapide sur le livre.
        $averageRating = Review::query()
            ->where('book_id', $book->id)
            ->where('is_visible', true)
            ->avg('rating');

        $book->forceFill([
            'average_rating' => round((float) ($averageRating ?? 0), 2),
        ])->save();
    }
}
