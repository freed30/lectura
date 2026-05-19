<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\Favorite;
use App\Models\ReadingHistory;
use App\Models\ReadingProgress;
use App\Models\UserPreference;
use App\Models\Wishlist;
use App\Services\RecommendationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    public function edit(Request $request, RecommendationService $recommendationService): Response
    {
        $user = $request->user();
        $user->load(['preferences', 'notifications']);

        $preferences = UserPreference::query()->firstOrCreate(
            ['user_id' => $user->id],
            $this->defaultPreferences()
        );

        $history = ReadingHistory::query()
            ->with(['book.author'])
            ->where('user_id', $user->id)
            ->latest('last_opened_at')
            ->get();

        $activeProgress = ReadingProgress::query()
            ->with(['book.author'])
            ->where('user_id', $user->id)
            ->where('progress_percent', '>', 0)
            ->where('is_finished', false)
            ->latest('last_read_at')
            ->get();

        $notifications = $user->notifications()
            ->latest()
            ->take(12)
            ->get();

        $favorites = Favorite::query()
            ->with(['book.author'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(8)
            ->get();

        $wishlist = Wishlist::query()
            ->with(['book.author'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(8)
            ->get();

        $favoriteGenres = $history
            ->flatMap(fn ($entry) => $entry->book?->genres ?? [])
            ->map(fn ($genre) => trim((string) $genre))
            ->filter()
            ->countBy()
            ->sortDesc()
            ->keys()
            ->take(4)
            ->values();

        $recentSearches = collect($preferences->search_history ?? [])
            ->map(fn ($term) => trim((string) $term))
            ->filter()
            ->take(6)
            ->values();

        $completedBooksCount = ReadingProgress::query()
            ->where('user_id', $user->id)
            ->where('is_finished', true)
            ->count();
        $historyCount = $history->count();
        $recentActivityCount = $history
            ->filter(fn ($entry) => $entry->last_opened_at?->greaterThanOrEqualTo(now()->subDays(7)))
            ->count();
        $completionRate = $historyCount > 0
            ? (int) round(($completedBooksCount / $historyCount) * 100)
            : 0;
        $profileCompletionPercent = (int) round((collect([
            filled($user->avatar),
            filled($user->bio),
            $historyCount > 0,
            $favorites->isNotEmpty() || $wishlist->isNotEmpty(),
            $recentSearches->isNotEmpty(),
        ])->filter()->count() / 5) * 100);

        return response()->view('profile.edit', [
            'user' => $user,
            'preferences' => $preferences,
            'history' => $history,
            'activeProgress' => $activeProgress,
            'completedBooksCount' => $completedBooksCount,
            'averageProgress' => round((float) $activeProgress->avg('progress_percent'), 1),
            'favoriteGenres' => $favoriteGenres,
            'recentSearches' => $recentSearches,
            'favorites' => $favorites,
            'wishlist' => $wishlist,
            'recommendations' => $recommendationService->recommendForUser($user, 6, [
                'search_terms' => $recentSearches->all(),
            ]),
            'notifications' => $notifications,
            'unreadNotificationsCount' => $user->unreadNotifications()->count(),
            'historyCount' => $historyCount,
            'recentActivityCount' => $recentActivityCount,
            'completionRate' => $completionRate,
            'profileCompletionPercent' => $profileCompletionPercent,
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'avatar' => $validated['avatar'] ?? null,
            'bio' => $validated['bio'] ?? null,
        ]);

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        $user->preferences()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'theme' => $validated['theme'],
                'font_size' => $validated['font_size'],
                'line_spacing' => $validated['line_spacing'],
                'page_flip_enabled' => $validated['page_flip_enabled'] ?? false,
                'immersive_mode_default' => $validated['immersive_mode_default'] ?? false,
            ]
        );

        return back()->with('status', 'Profil mis a jour avec succes.');
    }

    protected function defaultPreferences(): array
    {
        return [
            'theme' => 'dark',
            'font_size' => 'medium',
            'line_spacing' => 'comfortable',
            'page_flip_enabled' => true,
            'immersive_mode_default' => false,
            'search_history' => [],
        ];
    }
}
