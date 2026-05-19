<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class RecommendationsReadyNotification extends Notification
{
    use Queueable;

    public function __construct(protected Collection $recommendations)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $bookIds = $this->recommendations->pluck('book_id')->values()->all();
        $titles = $this->recommendations->pluck('book.title')->filter()->values()->take(3)->all();
        $count = count($bookIds);

        // logique : notifier l utilisateur quand de nouvelles suggestions sont prêtes.
        return [
            'category' => 'recommendation',
            'title' => 'Nouvelles recommandations',
            'message' => $count > 1
                ? "{$count} nouvelles suggestions personnalisees sont disponibles."
                : 'Une nouvelle suggestion personnalisee est disponible.',
            'book_ids' => $bookIds,
            'book_titles' => $titles,
            'count' => $count,
            'url' => route('reader.index'),
        ];
    }
}
