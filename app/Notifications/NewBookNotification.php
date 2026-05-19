<?php

namespace App\Notifications;

use App\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewBookNotification extends Notification
{
    use Queueable;

    public function __construct(protected Book $book)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        // logique : notifier l utilisateur quand un nouveau livre est disponible.
        return [
            'category' => 'new_book',
            'title' => 'Nouveau livre disponible',
            'message' => "Le livre \"{$this->book->title}\" vient d'arriver dans la bibliotheque.",
            'book_id' => $this->book->id,
            'book_title' => $this->book->title,
            'url' => route('reader.show', $this->book),
        ];
    }
}
