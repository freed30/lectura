<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookUploadRequest;
use App\Http\Requests\UpdateBookUploadRequest;
use App\Models\Author;
use App\Models\Book;
use App\Services\BookStorageService;
use App\Services\LibraryNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class BookUploadController extends Controller
{
    public function create(): Response
    {
        $admin = auth()->user();

        return response()->view('admin.books.create', [
            'admin' => $admin,
            'books' => $this->getRecentBooks(),
        ]);
    }

    public function store(
        StoreBookUploadRequest $request,
        LibraryNotificationService $libraryNotificationService,
        BookStorageService $bookStorageService
    ): RedirectResponse
    {
        $validated = $request->validated();

        $author = Author::query()->firstOrCreate([
            'name' => $validated['author_name'],
        ]);

        $file = $request->file('book_file');
        $extension = $this->detectFileExtension($file);
        $slug = $this->makeUniqueSlug($validated['title']);
        $storedCover = null;

        try {
            // stockage fichier sur le disque Laravel choisi pour le site.
            $storedFile = $bookStorageService->storeUploadedBook($file, $extension);
        } catch (\RuntimeException $exception) {
            return back()
                ->withErrors(['book_file' => $exception->getMessage()])
                ->withInput();
        }

        if ($request->hasFile('cover_file')) {
            try {
                // stockage image de couverture pour l affichage dans la bibliotheque.
                $storedCover = $bookStorageService->storeUploadedCover($request->file('cover_file'));
            } catch (\RuntimeException $exception) {
                $bookStorageService->deleteStoredBook($storedFile['file_disk'], $storedFile['fichier_path']);

                return back()
                    ->withErrors(['cover_file' => $exception->getMessage()])
                    ->withInput();
            }
        }

        try {
            $book = Book::query()->create([
                'author_id' => $author->id,
                'title' => $validated['title'],
                'slug' => $slug,
                'description' => $validated['description'] ?? null,
                'isbn' => $validated['isbn'] ?? null,
                'language' => $validated['language'] ?? 'fr',
                'genres' => $this->normalizeGenres($validated['genres'] ?? null),
                'cover_image' => $storedCover['cover_image'] ?? null,
                // recuperation du chemin stocke en base pour relire le livre sur le site.
                'fichier_path' => $storedFile['fichier_path'],
                'file_disk' => $storedFile['file_disk'],
                'file_format' => $extension,
                'file_mime_type' => $storedFile['file_mime_type'],
                'file_size' => $storedFile['file_size'],
                'page_count' => $validated['page_count'] ?? null,
                'published_at' => $validated['published_at'] ?? null,
                'price' => $validated['price'] ?? 0,
                'average_rating' => 0,
                'is_published' => $validated['is_published'] ?? true,
            ]);
        } catch (\Throwable $exception) {
            $bookStorageService->deleteStoredBook($storedFile['file_disk'], $storedFile['fichier_path']);
            $bookStorageService->deleteStoredAsset($storedCover['file_disk'] ?? null, $storedCover['cover_image'] ?? null);

            throw $exception;
        }

        if ($book->is_published) {
            $libraryNotificationService->notifyNewBook($book);
        }

        return redirect()
            ->route('admin.books.create')
            ->with('status', "Le livre \"{$book->title}\" a ete ajoute avec succes.");
    }

    public function edit(Book $book): Response
    {
        $admin = auth()->user();
        $book->load('author');

        return response()->view('admin.books.edit', [
            'admin' => $admin,
            'book' => $book,
            'books' => $this->getRecentBooks(),
        ]);
    }

    public function update(
        UpdateBookUploadRequest $request,
        Book $book,
        LibraryNotificationService $libraryNotificationService,
        BookStorageService $bookStorageService
    ): RedirectResponse
    {
        $validated = $request->validated();
        $wasPublished = (bool) $book->is_published;

        $author = Author::query()->firstOrCreate([
            'name' => $validated['author_name'],
        ]);

        $newStoredFile = null;
        $newExtension = null;
        $newStoredCover = null;

        if ($request->hasFile('book_file')) {
            $file = $request->file('book_file');
            $newExtension = $this->detectFileExtension($file);

            try {
                $newStoredFile = $bookStorageService->storeUploadedBook($file, $newExtension);
            } catch (\RuntimeException $exception) {
                return back()
                    ->withErrors(['book_file' => $exception->getMessage()])
                    ->withInput();
            }
        }

        if ($request->hasFile('cover_file')) {
            try {
                $newStoredCover = $bookStorageService->storeUploadedCover($request->file('cover_file'));
            } catch (\RuntimeException $exception) {
                if ($newStoredFile) {
                    $bookStorageService->deleteStoredBook($newStoredFile['file_disk'], $newStoredFile['fichier_path']);
                }

                return back()
                    ->withErrors(['cover_file' => $exception->getMessage()])
                    ->withInput();
            }
        }

        $oldStoredPath = $book->fichier_path;
        $oldStoredDisk = $book->file_disk;
        $oldCoverPath = $book->cover_image;

        try {
            $book->update([
                'author_id' => $author->id,
                'title' => $validated['title'],
                'slug' => $this->makeUniqueSlug($validated['title'], $book->id),
                'description' => $validated['description'] ?? null,
                'isbn' => $validated['isbn'] ?? null,
                'language' => $validated['language'] ?? 'fr',
                'genres' => $this->normalizeGenres($validated['genres'] ?? null),
                'cover_image' => $newStoredCover['cover_image'] ?? $book->cover_image,
                'page_count' => $validated['page_count'] ?? null,
                'published_at' => $validated['published_at'] ?? null,
                'price' => $validated['price'] ?? 0,
                'is_published' => $validated['is_published'] ?? false,
                // recuperation du nouveau chemin si un remplacement est fourni.
                'fichier_path' => $newStoredFile['fichier_path'] ?? $book->fichier_path,
                'file_disk' => $newStoredFile['file_disk'] ?? $book->file_disk,
                'file_format' => $newExtension ?? $book->file_format,
                'file_mime_type' => $newStoredFile['file_mime_type'] ?? $book->file_mime_type,
                'file_size' => $newStoredFile['file_size'] ?? $book->file_size,
            ]);
        } catch (\Throwable $exception) {
            if ($newStoredFile) {
                $bookStorageService->deleteStoredBook($newStoredFile['file_disk'], $newStoredFile['fichier_path']);
            }

            if ($newStoredCover) {
                $bookStorageService->deleteStoredAsset($newStoredCover['file_disk'], $newStoredCover['cover_image']);
            }

            throw $exception;
        }

        if ($newStoredFile && $oldStoredPath !== $newStoredFile['fichier_path']) {
            // suppression du fichier precedent apres remplacement reussi.
            $bookStorageService->deleteStoredBook($oldStoredDisk, $oldStoredPath);
        }

        if ($newStoredCover && $oldCoverPath !== $newStoredCover['cover_image']) {
            $bookStorageService->deleteStoredAsset($oldStoredDisk, $oldCoverPath);
        }

        if (! $wasPublished && $book->is_published) {
            $libraryNotificationService->notifyNewBook($book);
        }

        return redirect()
            ->route('admin.books.edit', $book)
            ->with('status', "Le livre \"{$book->title}\" a ete mis a jour.");
    }

    public function destroy(Book $book, BookStorageService $bookStorageService): RedirectResponse
    {
        $title = $book->title;
        $storedPath = $book->fichier_path;
        $storedDisk = $book->file_disk;
        $coverPath = $book->cover_image;

        $book->delete();
        $bookStorageService->deleteStoredBook($storedDisk, $storedPath);
        $bookStorageService->deleteStoredAsset($storedDisk, $coverPath);

        return redirect()
            ->route('admin.books.create')
            ->with('status', "Le livre \"{$title}\" a ete supprime.");
    }

    protected function getRecentBooks()
    {
        return Book::query()
            ->with('author')
            ->latest()
            ->take(12)
            ->get();
    }

    protected function makeUniqueSlug(string $title, ?int $ignoreBookId = null): string
    {
        $baseSlug = Str::slug($title) ?: 'livre';
        $slug = $baseSlug;
        $counter = 2;

        while (Book::query()
            ->where('slug', $slug)
            ->when($ignoreBookId, fn ($query) => $query->where('id', '!=', $ignoreBookId))
            ->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    protected function detectFileExtension(UploadedFile $file): string
    {
        return strtolower((string) $file->getClientOriginalExtension());
    }

    protected function normalizeGenres(?string $genres): array
    {
        return collect(explode(',', (string) $genres))
            ->map(fn ($genre) => trim(mb_strtolower($genre)))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
