<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\BookUploadController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReadingListController;
use App\Http\Controllers\ReaderController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->to(route('admin.dashboard', [], false))
            : redirect()->to(route('reader.index', [], false));
    }
    return redirect()->to(route('login', [], false));
});

Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login'])->name('login.store');
    Route::get('/inscription', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/inscription', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth.required')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');
    Route::post('/notifications/tout-lu', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/lue', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/lecteur/livres/{book}/avis', [ReviewController::class, 'store'])->name('reader.reviews.store');
    Route::post('/lecteur/livres/{book}/favori', [ReadingListController::class, 'toggleFavorite'])->name('reader.favorites.toggle');
    Route::post('/lecteur/livres/{book}/envie', [ReadingListController::class, 'toggleWishlist'])->name('reader.wishlist.toggle');
});

Route::middleware('auth.required')->prefix('lecteur')->name('reader.')->group(function () {
    Route::get('/', [ReaderController::class, 'index'])->name('index');
    Route::get('/livres/{book}', [ReaderController::class, 'show'])->name('show');
    Route::get('/livres/{book}/fichier', [ReaderController::class, 'asset'])->name('asset');
    Route::post('/livres/{book}/progression', [ReaderController::class, 'saveProgress'])->name('progress.store');
    Route::post('/livres/{book}/termine', [ReaderController::class, 'markFinished'])->name('progress.finish');
    Route::get('/stats', [ReaderController::class, 'stats'])->name('stats');
});

Route::middleware(['auth.required', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/livres/upload', [BookUploadController::class, 'create'])->name('books.create');
    Route::post('/livres/upload', [BookUploadController::class, 'store'])->name('books.store');
    Route::get('/livres/{book}/modifier', [BookUploadController::class, 'edit'])->name('books.edit');
    Route::put('/livres/{book}', [BookUploadController::class, 'update'])->name('books.update');
    Route::delete('/livres/{book}', [BookUploadController::class, 'destroy'])->name('books.destroy');
    Route::put('/lecteurs/{user}/role', [AdminDashboardController::class, 'updateRole'])->name('users.role');
    Route::post('/lecteurs/{user}/deconnecter', [AdminDashboardController::class, 'disconnect'])->name('users.disconnect');
    Route::delete('/lecteurs/{user}', [AdminDashboardController::class, 'destroy'])->name('users.destroy');
});
