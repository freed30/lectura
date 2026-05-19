<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\Recommendation;
use App\Models\ReadingHistory;
use App\Models\ReadingProgress;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Support\Collection;

class RecommendationService
{
    public function __construct(
        protected LibraryNotificationService $libraryNotificationService
    ) {
    }

    public function recommendForUser(User $user, int $limit = 6, array $context = []): Collection
    {
        $recommendations = $this->buildRecommendations($user, $limit, $context);

        return $this->persistRecommendations($user, $recommendations);
    }

    /**
     * Retourne le profil de genres du lecteur : liste de genres avec leur poids normalisé.
     */
    public function getGenreProfile(User $user): array
    {
        $signals = $this->collectReadingSignals($user);
        $genreWeights = $this->collectGenreWeights($signals);

        if (empty($genreWeights)) {
            return [];
        }

        $maxWeight = max($genreWeights);

        if ($maxWeight <= 0) {
            return [];
        }

        // normalisation en pourcentage pour l'affichage.
        $profile = [];
        foreach (array_slice($genreWeights, 0, 6, true) as $genre => $weight) {
            $profile[] = [
                'genre'   => ucfirst((string) $genre),
                'percent' => (int) round(($weight / $maxWeight) * 100),
                'weight'  => round($weight, 2),
            ];
        }

        return $profile;
    }

    /**
     * Résumé de l'activité de recommandation pour la vue (insights IA).
     */
    public function getRecommendationInsights(User $user): array
    {
        $signals        = $this->collectReadingSignals($user);
        $favoritesCount = Favorite::query()->where('user_id', $user->id)->count();
        $wishlistCount  = Wishlist::query()->where('user_id', $user->id)->count();
        $genreWeights   = $this->collectGenreWeights($signals);

        return [
            'reading_signals'  => $signals->count(),
            'favorites_count'  => $favoritesCount,
            'wishlist_count'   => $wishlistCount,
            'top_genres'       => array_keys(array_slice($genreWeights, 0, 3, true)),
            'signals_strength' => $signals->isEmpty() ? 'faible' : ($signals->count() >= 5 ? 'fort' : 'moyen'),
        ];
    }

    protected function buildRecommendations(User $user, int $limit, array $context = []): Collection
    {
        $signals      = $this->collectReadingSignals($user);
        $readBookIds  = $signals->keys()->map(fn ($bookId) => (int) $bookId)->values()->all();
        $genreWeights = $this->collectGenreWeights($signals);
        $searchWeights = $this->collectSearchWeights($context);

        // logique : recommander des livres similaires aux lectures déjà avancées.
        $similarBooks = $this->buildSimilarRecommendations(
            $signals,
            $genreWeights,
            $readBookIds,
            $limit
        );

        // logique : proposer aussi des livres proches des recherches et catégories récentes.
        $searchBooks = $this->buildSearchRecommendations(
            $searchWeights,
            $genreWeights,
            array_merge($readBookIds, $similarBooks->pluck('book_id')->all()),
            $limit
        );

        // logique : compléter avec des livres populaires pour garder des suggestions utiles.
        $popularBooks = $this->buildPopularRecommendations(
            $genreWeights,
            array_merge($readBookIds, $similarBooks->pluck('book_id')->all(), $searchBooks->pluck('book_id')->all()),
            $limit
        );

        return $similarBooks
            ->concat($searchBooks)
            ->concat($popularBooks)
            ->sortByDesc('score')
            ->unique('book_id')
            ->take($limit)
            ->values();
    }

    protected function collectReadingSignals(User $user): Collection
    {
        $signals = [];

        // signal 1 : progression de lecture.
        $readingProgress = ReadingProgress::query()
            ->with('book')
            ->where('user_id', $user->id)
            ->get();

        foreach ($readingProgress as $progress) {
            if (! $progress->book || ! $progress->book->is_published) {
                continue;
            }

            $bookId = (int) $progress->book_id;
            $signals[$bookId] = [
                'book'             => $progress->book,
                'progress_percent' => max(
                    (float) ($signals[$bookId]['progress_percent'] ?? 0),
                    (float) $progress->progress_percent
                ),
                'source' => $signals[$bookId]['source'] ?? 'progress',
            ];
        }

        // signal 2 : historique d'ouverture.
        $readingHistory = ReadingHistory::query()
            ->with('book')
            ->where('user_id', $user->id)
            ->get();

        foreach ($readingHistory as $history) {
            if (! $history->book || ! $history->book->is_published) {
                continue;
            }

            $bookId = (int) $history->book_id;
            $signals[$bookId] = [
                'book'             => $history->book,
                'progress_percent' => max(
                    (float) ($signals[$bookId]['progress_percent'] ?? 0),
                    (float) $history->last_progress_percent
                ),
                'source' => $signals[$bookId]['source'] ?? 'history',
            ];
        }

        // signal 3 : favoris — poids élevé car fort signal d'intention.
        $favorites = Favorite::query()
            ->with('book')
            ->where('user_id', $user->id)
            ->get();

        foreach ($favorites as $favorite) {
            if (! $favorite->book || ! $favorite->book->is_published) {
                continue;
            }

            $bookId = (int) $favorite->book_id;
            if (isset($signals[$bookId])) {
                // renforcer le signal existant.
                $signals[$bookId]['progress_percent'] = min(100, $signals[$bookId]['progress_percent'] + 25);
                $signals[$bookId]['source']           = 'favorite';
            } else {
                $signals[$bookId] = [
                    'book'             => $favorite->book,
                    'progress_percent' => 50.0, // favoris = engagement moyen estimé.
                    'source'           => 'favorite',
                ];
            }
        }

        // signal 4 : liste de souhait — signal d'intention plus faible mais réel.
        $wishlists = Wishlist::query()
            ->with('book')
            ->where('user_id', $user->id)
            ->get();

        foreach ($wishlists as $wishlist) {
            if (! $wishlist->book || ! $wishlist->book->is_published) {
                continue;
            }

            $bookId = (int) $wishlist->book_id;
            if (! isset($signals[$bookId])) {
                $signals[$bookId] = [
                    'book'             => $wishlist->book,
                    'progress_percent' => 20.0, // wishlist = intention découverte faible.
                    'source'           => 'wishlist',
                ];
            }
        }

        // logique : plus la progression est forte, plus le livre influence les suggestions.
        return collect($signals)->map(function (array $signal) {
            $progress = (float) $signal['progress_percent'];
            $weight   = max(0.25, min(1.6, ($progress / 100) + 0.35));

            if ($progress >= 95) {
                $weight += 0.25;
            }

            // bonus pour les favoris explicitement ajoutés.
            if (($signal['source'] ?? '') === 'favorite') {
                $weight += 0.3;
            }

            $signal['weight'] = $weight;

            return $signal;
        });
    }

    protected function collectGenreWeights(Collection $signals): array
    {
        $genreWeights = [];

        foreach ($signals as $signal) {
            /** @var Book $book */
            $book = $signal['book'];

            foreach ($book->normalizedGenres() as $genre) {
                $genreWeights[$genre] = ($genreWeights[$genre] ?? 0) + (float) $signal['weight'];
            }
        }

        arsort($genreWeights);

        return $genreWeights;
    }

    protected function buildSimilarRecommendations(
        Collection $signals,
        array $genreWeights,
        array $excludedBookIds,
        int $limit
    ): Collection {
        if ($signals->isEmpty() || empty($genreWeights)) {
            return collect();
        }

        $candidates = Book::query()
            ->withCount(['favorites', 'reviews', 'readingHistory'])
            ->where('is_published', true)
            ->whereNotIn('id', $excludedBookIds)
            ->get();

        return $candidates
            ->map(function (Book $candidate) use ($signals, $genreWeights) {
                $candidateGenres = $candidate->normalizedGenres();

                if (empty($candidateGenres)) {
                    return null;
                }

                $bestSourceBook = null;
                $bestScore      = 0.0;

                foreach ($signals as $signal) {
                    /** @var Book $sourceBook */
                    $sourceBook   = $signal['book'];
                    $sharedGenres = array_intersect($candidateGenres, $sourceBook->normalizedGenres());

                    if (empty($sharedGenres)) {
                        continue;
                    }

                    $genreScore = collect($sharedGenres)
                        ->sum(fn ($genre) => (float) ($genreWeights[$genre] ?? 0));

                    $score = ($genreScore * 18) + ((float) $signal['weight'] * 12);

                    if ($candidate->language === $sourceBook->language) {
                        $score += 4;
                    }

                    if ($score > $bestScore) {
                        $bestScore      = $score;
                        $bestSourceBook = $sourceBook;
                    }
                }

                if (! $bestSourceBook) {
                    return null;
                }

                return [
                    'book_id'          => $candidate->id,
                    'based_on_book_id' => $bestSourceBook->id,
                    'reason'           => $this->buildSimilarReason($bestSourceBook, $signals),
                    'score'            => round($bestScore + ($this->popularityValue($candidate) * 0.2), 2),
                ];
            })
            ->filter()
            ->sortByDesc('score')
            ->take($limit)
            ->values();
    }

    protected function buildPopularRecommendations(
        array $genreWeights,
        array $excludedBookIds,
        int $limit
    ): Collection {
        $candidates = Book::query()
            ->withCount(['favorites', 'reviews', 'readingHistory'])
            ->where('is_published', true)
            ->whereNotIn('id', array_values(array_unique($excludedBookIds)))
            ->get();

        return $candidates
            ->map(function (Book $candidate) use ($genreWeights) {
                $genreBonus = collect($candidate->normalizedGenres())
                    ->sum(fn ($genre) => (float) ($genreWeights[$genre] ?? 0) * 2);

                $score = round($this->popularityValue($candidate) + $genreBonus, 2);

                if ($score <= 0) {
                    return null;
                }

                $reason = $genreBonus > 0
                    ? 'Populaire dans un genre que vous appréciez'
                    : 'Titre populaire dans la bibliothèque';

                return [
                    'book_id'          => $candidate->id,
                    'based_on_book_id' => null,
                    'reason'           => $reason,
                    'score'            => $score,
                ];
            })
            ->filter()
            ->sortByDesc('score')
            ->take($limit)
            ->values();
    }

    protected function buildSearchRecommendations(
        array $searchWeights,
        array $genreWeights,
        array $excludedBookIds,
        int $limit
    ): Collection {
        if (empty($searchWeights)) {
            return collect();
        }

        $candidates = Book::query()
            ->with(['author'])
            ->withCount(['favorites', 'reviews', 'readingHistory'])
            ->where('is_published', true)
            ->whereNotIn('id', array_values(array_unique($excludedBookIds)))
            ->get();

        return $candidates
            ->map(function (Book $candidate) use ($searchWeights, $genreWeights) {
                $title           = mb_strtolower((string) $candidate->title);
                $author          = mb_strtolower((string) ($candidate->author?->name ?? ''));
                $description     = mb_strtolower((string) ($candidate->description ?? ''));
                $candidateGenres = $candidate->normalizedGenres();
                $directScore     = 0.0;
                $genreHit        = false;
                $textHit         = false;

                foreach ($searchWeights as $term => $weight) {
                    if (str_contains($title, $term)) {
                        $directScore += 28 * $weight;
                        $textHit      = true;
                    }

                    if ($author !== '' && str_contains($author, $term)) {
                        $directScore += 24 * $weight;
                        $textHit      = true;
                    }

                    if ($description !== '' && str_contains($description, $term)) {
                        $directScore += 10 * $weight;
                    }

                    foreach ($candidateGenres as $genre) {
                        if (str_contains($genre, $term) || str_contains($term, $genre)) {
                            $directScore += 20 * $weight;
                            $genreHit    = true;
                            break;
                        }
                    }
                }

                $readerGenreBonus = collect($candidateGenres)
                    ->sum(fn ($genre) => (float) ($genreWeights[$genre] ?? 0) * 2.5);

                $score = round($directScore + $readerGenreBonus + ($this->popularityValue($candidate) * 0.16), 2);

                if ($score <= 0 || ($directScore <= 0 && $readerGenreBonus <= 0)) {
                    return null;
                }

                $reason = $genreHit && ! $textHit
                    ? 'Correspond à une catégorie de vos recherches récentes'
                    : ($textHit
                        ? 'Lié directement à vos recherches récentes'
                        : 'Recommandé selon vos catégories favorites');

                return [
                    'book_id'          => $candidate->id,
                    'based_on_book_id' => null,
                    'reason'           => $reason,
                    'score'            => $score,
                ];
            })
            ->filter()
            ->sortByDesc('score')
            ->take($limit)
            ->values();
    }

    /**
     * Construit un label de raison enrichi avec le titre du livre source.
     */
    protected function buildSimilarReason(Book $sourceBook, Collection $signals): string
    {
        $signal = $signals->get($sourceBook->id);

        if (! $signal) {
            return 'Livre similaire à vos lectures récentes';
        }

        $source   = $signal['source'] ?? 'progress';
        $progress = (float) ($signal['progress_percent'] ?? 0);
        $title    = \Illuminate\Support\Str::limit($sourceBook->title, 30);

        return match ($source) {
            'favorite' => "Parce que vous avez mis « {$title} » en favori",
            'wishlist'  => "Similaire à « {$title} » dans votre liste de lecture",
            default     => $progress >= 80
                ? "Parce que vous avez lu {$progress}% de « {$title} »"
                : "Similaire à « {$title} » que vous lisez",
        };
    }

    protected function popularityValue(Book $book): float
    {
        return
            ((float) $book->average_rating * 10)
            + ((int) $book->reviews_count * 4)
            + ((int) $book->favorites_count * 4)
            + ((int) $book->reading_history_count * 3);
    }

    protected function collectSearchWeights(array $context): array
    {
        $historyTerms = $this->normalizeSearchTerms((array) ($context['search_terms'] ?? []));
        $activeTerms  = $this->normalizeSearchTerms([(string) ($context['active_search'] ?? '')]);
        $weights      = [];

        foreach ($historyTerms as $term) {
            $weights[$term] = max((float) ($weights[$term] ?? 0), 1.0);
        }

        foreach ($activeTerms as $term) {
            $weights[$term] = max((float) ($weights[$term] ?? 0), 1.8);
        }

        arsort($weights);

        return $weights;
    }

    protected function normalizeSearchTerms(array $terms): array
    {
        return collect($terms)
            ->flatMap(function ($term) {
                $normalized = trim(mb_strtolower((string) $term));

                if ($normalized === '') {
                    return [];
                }

                $tokens = preg_split('/[\s,;:|\/\\\\]+/u', $normalized) ?: [];

                return array_merge([$normalized], $tokens);
            })
            ->map(fn ($term) => trim((string) $term))
            ->filter(fn ($term) => mb_strlen($term) >= 2)
            ->unique()
            ->values()
            ->all();
    }

    protected function persistRecommendations(User $user, Collection $recommendations): Collection
    {
        $bookIds = $recommendations->pluck('book_id')->all();

        if (empty($bookIds)) {
            Recommendation::query()
                ->where('user_id', $user->id)
                ->delete();

            return collect();
        }

        Recommendation::query()
            ->where('user_id', $user->id)
            ->whereNotIn('book_id', $bookIds)
            ->delete();

        $newRecommendationIds = collect();

        foreach ($recommendations as $data) {
            $recommendation = Recommendation::query()->firstOrNew([
                'user_id' => $user->id,
                'book_id' => $data['book_id'],
            ]);

            $isNewRecommendation = ! $recommendation->exists;

            $recommendation->based_on_book_id = $data['based_on_book_id'];
            $recommendation->reason           = $data['reason'];
            $recommendation->score            = $data['score'];

            if (! $recommendation->exists) {
                $recommendation->is_seen = false;
            }

            $recommendation->save();

            if ($isNewRecommendation) {
                $newRecommendationIds->push($recommendation->book_id);
            }
        }

        $storedRecommendations = Recommendation::query()
            ->with(['book.author', 'basedOnBook'])
            ->where('user_id', $user->id)
            ->whereIn('book_id', $bookIds)
            ->orderByDesc('score')
            ->get();

        if ($newRecommendationIds->isNotEmpty()) {
            $this->libraryNotificationService->notifyNewRecommendations(
                $user,
                $storedRecommendations->whereIn('book_id', $newRecommendationIds->all())->values()
            );
        }

        return $storedRecommendations;
    }
}
