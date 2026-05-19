<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\ReadingHistory;
use App\Models\ReadingProgress;
use App\Models\Review;
use App\Models\User;
use App\Models\UserPreference;
use App\Services\BookStorageService;
use App\Services\RecommendationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReaderController extends Controller
{
    public function index(Request $request, RecommendationService $recommendationService): Response
    {
        /** @var User|null $reader */
        $reader = auth()->user();

        $search      = trim((string) $request->query('search', ''));
        $sortBy      = $request->query('sort', 'title');       // title|recent|rating|popular
        $filterFormat = $request->query('format', '');         // pdf|epub|''
        $filterType   = $request->query('filter', '');         // favorites|wishlist|''
        $searchTerms = $this->extractSearchTerms($search);
        $searchHistory = [];

        if ($reader) {
            $preferences = UserPreference::query()->firstOrCreate(
                ['user_id' => $reader->id],
                $this->defaultPreferences()
            );

            $searchHistory = $this->rememberSearchHistory($preferences, $search);
        }

        $catalogQuery = Book::query()->where('is_published', true);

        $booksQuery = Book::query()
            ->with([
                'author',
                'readingProgress' => fn ($query) => $reader
                    ? $query->where('user_id', $reader->id)
                    : $query->whereRaw('1 = 0'),
            ])
            ->withCount([
                'reviews as visible_reviews_count' => fn ($query) => $query->where('is_visible', true),
                'favorites',
            ])
            ->where('is_published', true);

        // ── Filtre format ──
        if ($filterFormat !== '') {
            $booksQuery->where('file_format', $filterFormat);
        }

        // ── Filtre type (Favoris / Wishlist) ──
        if ($reader && $filterType === 'favorites') {
            $booksQuery->whereHas('favorites', fn ($q) => $q->where('user_id', $reader->id));
        } elseif ($reader && $filterType === 'wishlist') {
            $booksQuery->whereHas('wishlists', fn ($q) => $q->where('user_id', $reader->id));
        }

        // ── Recherche ──
        if (! empty($searchTerms)) {
            $booksQuery->where(function ($query) use ($searchTerms): void {
                foreach ($searchTerms as $term) {
                    $searchLike = '%' . $term . '%';
                    $query->where(function ($subQuery) use ($searchLike): void {
                        $subQuery
                            ->where('title', 'like', $searchLike)
                            ->orWhere('description', 'like', $searchLike)
                            ->orWhereRaw('LOWER(COALESCE(CAST(genres AS CHAR), "")) like ?', [mb_strtolower($searchLike)])
                            ->orWhereHas('author', fn ($q) => $q->where('name', 'like', $searchLike));
                    });
                }
            });
        }

        // ── Tri ──
        $booksQuery = match ($sortBy) {
            'recent'  => $booksQuery->orderByDesc('published_at'),
            'rating'  => $booksQuery->orderByDesc('average_rating'),
            'popular' => $booksQuery->orderByDesc('favorites_count'),
            default   => $booksQuery->orderBy('title'),
        };

        $books = $booksQuery->paginate(12)->withQueryString();

        $recommendations = $reader
            ? $recommendationService->recommendForUser($reader, 6, [
                'active_search' => $search,
                'search_terms'  => $searchHistory,
            ])
            : collect();

        $favoriteBookIds = $reader
            ? $reader->favorites()->pluck('book_id')->map(fn ($id) => (int) $id)->all()
            : [];

        $wishlistBookIds = $reader
            ? $reader->wishlists()->pluck('book_id')->map(fn ($id) => (int) $id)->all()
            : [];

        // ── Livres en cours ──
        $continueReading = $books
            ->filter(function ($book) {
                $progress = (float) ($book->readingProgress->first()?->progress_percent ?? 0);
                return $progress > 0 && $progress < 100;
            })
            ->sortByDesc(fn ($book) => (float) ($book->readingProgress->first()?->progress_percent ?? 0))
            ->take(6)
            ->values();

        // ── Livres terminés ──
        $finishedBooks = $reader
            ? ReadingProgress::query()
                ->where('user_id', $reader->id)
                ->where('is_finished', true)
                ->count()
            : 0;

        // ── Pages lues totales (approximation) ──
        $totalPagesRead = $reader
            ? ReadingProgress::query()
                ->where('user_id', $reader->id)
                ->sum('current_page')
            : 0;

        // ── Profil de genres ──
        $genreProfile = $reader
            ? $recommendationService->getGenreProfile($reader)
            : [];

        // ── Insights IA ──
        $recommendationInsights = $reader
            ? $recommendationService->getRecommendationInsights($reader)
            : [];

        // ── Favoris récents (sidebar) ──
        $recentFavorites = $reader
            ? Favorite::query()
                ->with('book.author')
                ->where('user_id', $reader->id)
                ->latest()
                ->take(4)
                ->get()
                ->filter(fn ($fav) => $fav->book && $fav->book->is_published)
                ->values()
            : collect();

        // ── Livre à la une (meilleure note parmi publiés) ──
        $featuredBook = (clone $catalogQuery)
            ->orderByDesc('average_rating')
            ->orderByDesc('published_at')
            ->first();

        return response()->view('reader.index', [
            'books'                    => $books,
            'featuredBook'             => $featuredBook,
            'reader'                   => $reader,
            'readerName'               => $reader?->name ?? 'Visiteur',
            'recommendations'          => $recommendations,
            'recommendationInsights'   => $recommendationInsights,
            'genreProfile'             => $genreProfile,
            'continueReading'          => $continueReading,
            'recentFavorites'          => $recentFavorites,
            'search'                   => $search,
            'sortBy'                   => $sortBy,
            'filterFormat'             => $filterFormat,
            'filterType'               => $filterType,
            'searchHistory'            => $searchHistory,
            'hasActiveFilters'         => $search !== '' || $filterFormat !== '' || $filterType !== '',
            'totalPublishedBooks'      => (clone $catalogQuery)->count(),
            'totalPdfBooks'            => (clone $catalogQuery)->where('file_format', 'pdf')->count(),
            'totalEpubBooks'           => (clone $catalogQuery)->where('file_format', 'epub')->count(),
            'finishedBooks'            => $finishedBooks,
            'totalPagesRead'           => (int) $totalPagesRead,
            'favoriteBookIds'          => $favoriteBookIds,
            'wishlistBookIds'          => $wishlistBookIds,
            'unreadNotificationsCount' => $reader?->unreadNotifications()->count() ?? 0,
        ]);
    }

    public function show(Book $book, BookStorageService $bookStorageService): Response
    {
        abort_unless($book->is_published, 404);

        $reader     = $this->resolveReaderUser();
        $progress   = $book->getUserProgress($reader->id);
        $preferences = UserPreference::query()->firstOrCreate(
            ['user_id' => $reader->id],
            $this->defaultPreferences()
        );
        $book->load([
            'author',
            'reviews' => fn ($query) => $query
                ->with('user')
                ->where('is_visible', true)
                ->latest()
                ->take(6),
        ])->loadCount([
            'reviews as visible_reviews_count' => fn ($query) => $query->where('is_visible', true),
        ]);

        $userReview = auth()->check()
            ? Review::query()
                ->where('user_id', auth()->id())
                ->where('book_id', $book->id)
                ->first()
            : null;

        $isFavorite = $reader->favorites()
            ->where('book_id', $book->id)
            ->exists();

        $isInWishlist = $reader->wishlists()
            ->where('book_id', $book->id)
            ->exists();

        abort_unless(
            $book->hasRemoteFile() || $bookStorageService->exists($book),
            404,
            'Le fichier du livre est introuvable.'
        );

        return response()->view('reader.show', [
            'book'          => $book,
            'reader'        => $reader,
            'progress'      => $progress,
            'preferences'   => $preferences,
            'userReview'    => $userReview,
            'isFavorite'    => $isFavorite,
            'isInWishlist'  => $isInWishlist,
            'readerFileUrl' => $book->hasRemoteFile()
                ? $book->fichier_path
                : route('reader.asset', $book, false),
        ]);
    }

    public function asset(Book $book, BookStorageService $bookStorageService): Response
    {
        abort_unless($book->is_published, 404);

        if ($book->hasRemoteFile()) {
            return redirect()->away($book->fichier_path);
        }

        $response = $bookStorageService->responseForReader($book);

        abort_unless($response, 404, 'Le fichier du livre est introuvable.');

        return $response;
    }

    public function saveProgress(Request $request, Book $book): JsonResponse
    {
        abort_unless($book->is_published, 404);

        $reader = $this->resolveReaderUser();

        $validated = $request->validate([
            'current_page'     => ['nullable', 'integer', 'min:0'],
            'current_location' => ['nullable', 'string', 'max:2048'],
            'total_pages'      => ['nullable', 'integer', 'min:0'],
            'progress_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_finished'      => ['nullable', 'boolean'],
        ]);

        $totalPages  = max((int) ($validated['total_pages'] ?? ($book->page_count ?? 0)), 0);
        $currentPage = max((int) ($validated['current_page'] ?? 0), 0);

        if ($totalPages > 0) {
            $currentPage = min($currentPage, $totalPages);
        }

        $progressPercent = array_key_exists('progress_percent', $validated)
            ? round((float) $validated['progress_percent'], 2)
            : ($totalPages > 0
                ? round(($currentPage / $totalPages) * 100, 2)
                : 0.0);

        $isFinished = (bool) ($validated['is_finished'] ?? ($progressPercent >= 99.5));

        $progress = ReadingProgress::query()->firstOrNew([
            'user_id' => $reader->id,
            'book_id' => $book->id,
        ]);

        $progress->fill([
            'current_page'     => $currentPage,
            'current_location' => $validated['current_location'] ?? null,
            'total_pages'      => $totalPages,
            'progress_percent' => $progressPercent,
            'is_finished'      => $isFinished,
            'last_read_at'     => now(),
        ]);
        $progress->completed_at = $isFinished
            ? ($progress->completed_at ?? now())
            : null;
        $progress->save();

        $history = ReadingHistory::query()->firstOrNew([
            'user_id' => $reader->id,
            'book_id' => $book->id,
        ]);

        if (! $history->exists) {
            $history->started_at = now();
        }

        $history->last_opened_at        = now();
        $history->last_progress_percent = $progressPercent;
        $history->completed_at          = $isFinished ? now() : $history->completed_at;
        $history->save();

        return response()->json([
            'saved'    => true,
            'progress' => $progress,
        ]);
    }

    /**
     * Marquer un livre comme terminé manuellement.
     */
    public function markFinished(Request $request, Book $book): JsonResponse
    {
        abort_unless($book->is_published, 404);

        $reader = $this->resolveReaderUser();

        $progress = ReadingProgress::query()->updateOrCreate(
            ['user_id' => $reader->id, 'book_id' => $book->id],
            [
                'progress_percent' => 100.0,
                'is_finished'      => true,
                'completed_at'     => now(),
                'last_read_at'     => now(),
            ]
        );

        return response()->json([
            'marked'  => true,
            'message' => "« {$book->title} » marqué comme terminé.",
            'progress' => $progress,
        ]);
    }

    /**
     * Statistiques de lecture du lecteur (JSON).
     */
    public function stats(Request $request): JsonResponse
    {
        $reader = $this->resolveReaderUser();

        $progressRecords = ReadingProgress::query()
            ->where('user_id', $reader->id)
            ->with('book')
            ->get();

        return response()->json([
            'books_started'   => $progressRecords->where('progress_percent', '>', 0)->count(),
            'books_finished'  => $progressRecords->where('is_finished', true)->count(),
            'total_pages_read'=> (int) $progressRecords->sum('current_page'),
            'favorites_count' => $reader->favorites()->count(),
            'wishlist_count'  => $reader->wishlists()->count(),
            'avg_progress'    => round($progressRecords->avg('progress_percent') ?? 0, 1),
        ]);
    }

    protected function resolveReaderUser(): User
    {
        /** @var User|null $user */
        $user = auth()->user();

        abort_unless($user, 403);

        return $user;
    }

    protected function defaultPreferences(): array
    {
        return [
            'theme'                  => 'dark',
            'font_size'              => 'medium',
            'line_spacing'           => 'comfortable',
            'page_flip_enabled'      => true,
            'immersive_mode_default' => false,
            'search_history'         => [],
        ];
    }

    protected function extractSearchTerms(string $search): array
    {
        return collect(preg_split('/[\s,;:|\/\\\\]+/u', mb_strtolower($search)) ?: [])
            ->map(fn ($term) => trim((string) $term))
            ->filter(fn ($term) => mb_strlen($term) >= 2)
            ->unique()
            ->values()
            ->all();
    }

    protected function rememberSearchHistory(UserPreference $preferences, string $search): array
    {
        $existingHistory = collect($preferences->search_history ?? [])
            ->map(fn ($term) => trim((string) $term))
            ->filter()
            ->values();

        if ($search === '') {
            return $existingHistory->all();
        }

        $updatedHistory = $existingHistory
            ->prepend($search)
            ->unique(fn ($term) => mb_strtolower((string) $term))
            ->take(8)
            ->values();

        $preferences->forceFill([
            'search_history' => $updatedHistory->all(),
        ])->save();

        return $updatedHistory->all();
    }
}
