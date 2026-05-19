<?php

namespace App\Services;

use App\Models\Book;
use App\Models\User;
use App\Notifications\NewBookNotification;
use App\Notifications\RecommendationsReadyNotification;
use Illuminate\Support\Collection;

class LibraryNotificationService
{
    public function notifyNewRecommendations(User $user, Collection $recommendations): void
    {
        if ($recommendations->isEmpty()) {
            return;
        }

        // logique : enregistrer en base une notification pour les nouvelles recommandations.
        $user->notify(new RecommendationsReadyNotification($recommendations));
    }

    public function notifyNewBook(Book $book): void
    {
        if (! $book->is_published) {
            return;
        }

        // logique : notifier les lecteurs qu un nouveau livre est disponible.
        User::query()
            ->where('role', 'reader')
            ->orderBy('id')
            ->chunkById(100, function ($users) use ($book): void {
                foreach ($users as $user) {
                    $user->notify(new NewBookNotification($book));
                }
            });
    }
}
