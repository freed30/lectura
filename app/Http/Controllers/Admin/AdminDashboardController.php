<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Favorite;
use App\Models\ReadingHistory;
use App\Models\ReadingProgress;
use App\Models\Review;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class AdminDashboardController extends Controller
{
    public function index(): Response
    {
        $activeThreshold = now()->subMinutes(20)->timestamp;
        $sessionDriver = (string) config('session.driver', 'file');
        $sessionTable = (string) config('session.table', 'sessions');
        $sessionTrackingEnabled = $sessionDriver === 'database' && Schema::hasTable($sessionTable);

        $sessionRows = $sessionTrackingEnabled
            ? DB::table($sessionTable . ' as sessions')
                ->join('users as users', 'users.id', '=', 'sessions.user_id')
                ->whereNotNull('sessions.user_id')
                ->select([
                    'users.id as user_id',
                    'users.name',
                    'users.email',
                    'users.role',
                    'sessions.ip_address',
                    'sessions.user_agent',
                    'sessions.last_activity',
                ])
                ->orderByDesc('sessions.last_activity')
                ->get()
            : collect();

        $connectedUsers = $sessionRows
            ->where('last_activity', '>=', $activeThreshold)
            ->groupBy('user_id')
            ->map(function ($sessions) {
                $latestSession = $sessions->sortByDesc('last_activity')->first();

                return (object) [
                    'user_id' => $latestSession->user_id,
                    'name' => $latestSession->name,
                    'email' => $latestSession->email,
                    'role' => $latestSession->role,
                    'ip_address' => $latestSession->ip_address,
                    'user_agent' => $latestSession->user_agent,
                    'last_activity' => (int) $latestSession->last_activity,
                    'last_seen_human' => Carbon::createFromTimestamp((int) $latestSession->last_activity)->diffForHumans(),
                    'sessions_count' => $sessions->count(),
                ];
            });

        $users = User::query()
            ->withCount([
                'favorites',
                'wishlists',
                'readingHistory',
                'readingProgress as active_progress_count' => fn ($query) => $query
                    ->where('progress_percent', '>', 0)
                    ->where('is_finished', false),
                'readingProgress as completed_books_count' => fn ($query) => $query
                    ->where('is_finished', true),
            ])
            ->latest()
            ->get()
            ->map(function (User $user) use ($connectedUsers, $sessionTrackingEnabled) {
                $session = $connectedUsers->get($user->id);
                $engagementScore = ($user->completed_books_count * 5)
                    + ($user->active_progress_count * 3)
                    + ($user->reading_history_count * 2)
                    + $user->favorites_count
                    + $user->wishlists_count;

                $user->setAttribute('is_connected', (bool) $session);
                $user->setAttribute(
                    'last_seen_human',
                    $session?->last_seen_human ?? ($sessionTrackingEnabled ? 'hors ligne' : 'suivi indisponible')
                );
                $user->setAttribute('sessions_count', $session?->sessions_count ?? 0);
                $user->setAttribute('ip_address', $session?->ip_address ?? null);
                $user->setAttribute('user_agent', $session?->user_agent ?? null);
                $user->setAttribute('engagement_score', $engagementScore);

                return $user;
            });

        $connectedReaders = $connectedUsers
            ->where('role', 'reader')
            ->values();

        $topReaders = $users
            ->where('role', 'reader')
            ->sortByDesc('engagement_score')
            ->take(5)
            ->values();

        $recentHistory = ReadingHistory::query()
            ->with(['user', 'book.author'])
            ->latest('last_opened_at')
            ->take(10)
            ->get();

        $recentBooks = Book::query()
            ->with('author')
            ->latest()
            ->take(8)
            ->get();

        $totalUsers = $users->count();
        $totalReaders = (int) $users->where('role', 'reader')->count();
        $totalAdmins = (int) $users->where('role', 'admin')->count();
        $totalBooks = Book::query()->count();
        $publishedBooks = Book::query()->where('is_published', true)->count();
        $activeReadings = ReadingProgress::query()
            ->where('progress_percent', '>', 0)
            ->where('is_finished', false)
            ->count();
        $completedBooks = ReadingProgress::query()
            ->where('is_finished', true)
            ->count();
        $reviewsCount = Review::query()->count();
        $favoritesCount = Favorite::query()->count();
        $wishlistsCount = Wishlist::query()->count();
        $averageReviewRating = round((float) Review::query()->avg('rating'), 1);
        $averageReadingProgress = round((float) ReadingProgress::query()->avg('progress_percent'), 1);
        $recentUploads = Book::query()
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        $formatBreakdown = Book::query()
            ->select('file_format', DB::raw('COUNT(*) as total'))
            ->groupBy('file_format')
            ->pluck('total', 'file_format');

        return response()->view('admin.dashboard', [
            'admin' => auth()->user(),
            'users' => $users,
            'connectedReaders' => $connectedReaders,
            'recentHistory' => $recentHistory,
            'recentBooks' => $recentBooks,
            'topReaders' => $topReaders,
            'stats' => [
                'total_users' => $totalUsers,
                'total_readers' => $totalReaders,
                'total_admins' => $totalAdmins,
                'connected_readers' => $connectedReaders->count(),
                'connected_users' => $connectedUsers->count(),
                'total_books' => $totalBooks,
                'published_books' => $publishedBooks,
                'active_readings' => $activeReadings,
                'reviews' => $reviewsCount,
                'favorites' => $favoritesCount,
                'wishlists' => $wishlistsCount,
            ],
            'insights' => [
                'session_tracking_enabled' => $sessionTrackingEnabled,
                'session_driver' => $sessionDriver,
                'connected_rate' => $totalUsers > 0 ? (int) round(($connectedUsers->count() / $totalUsers) * 100) : 0,
                'published_rate' => $totalBooks > 0 ? (int) round(($publishedBooks / $totalBooks) * 100) : 0,
                'average_review_rating' => $averageReviewRating,
                'average_reading_progress' => $averageReadingProgress,
                'interaction_volume' => $reviewsCount + $favoritesCount + $wishlistsCount,
                'pending_books' => max($totalBooks - $publishedBooks, 0),
                'recent_uploads' => $recentUploads,
                'completed_books' => $completedBooks,
                'formats' => $formatBreakdown,
            ],
        ]);
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', Rule::in(['reader', 'admin'])],
        ]);

        if ((int) $request->user()->id === (int) $user->id && $validated['role'] !== 'admin') {
            return back()->with('warning', 'Vous ne pouvez pas retirer vos propres droits administrateur.');
        }

        $user->update([
            'role' => $validated['role'],
        ]);

        DB::table('sessions')
            ->where('user_id', $user->id)
            ->delete();

        return back()->with('status', "Le role de {$user->name} a ete mis a jour.");
    }

    public function disconnect(User $user): RedirectResponse
    {
        $deletedSessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->delete();

        return back()->with(
            'status',
            $deletedSessions > 0
                ? "{$user->name} a ete deconnecte du site."
                : "{$user->name} n'avait aucune session active."
        );
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ((int) $request->user()->id === (int) $user->id) {
            return back()->with('warning', 'Vous ne pouvez pas supprimer votre propre compte administrateur.');
        }

        if ($user->isAdmin() && User::query()->where('role', 'admin')->count() <= 1) {
            return back()->with('warning', 'Au moins un administrateur doit rester actif sur le site.');
        }

        $name = $user->name;

        DB::table('sessions')
            ->where('user_id', $user->id)
            ->delete();

        $user->delete();

        return back()->with('status', "Le compte de {$name} a ete supprime.");
    }
}
