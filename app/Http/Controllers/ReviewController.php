<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Book;
use App\Services\BookReviewService;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, Book $book, BookReviewService $bookReviewService): RedirectResponse
    {
        abort_unless($book->is_published, 404);

        $bookReviewService->storeForUser(
            $book,
            $request->user(),
            $request->validated()
        );

        return redirect()
            ->route('reader.show', $book)
            ->with('review_status', 'Votre avis a ete enregistre.');
    }
}
