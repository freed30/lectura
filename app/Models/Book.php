<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'author_id',
    'title',
    'slug',
    'description',
    'isbn',
    'language',
    'genres',
    'cover_image',
    'fichier_path',
    'file_disk',
    'file_format',
    'file_mime_type',
    'file_size',
    'page_count',
    'published_at',
    'price',
    'average_rating',
    'is_published',
])]
class Book extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'published_at' => 'date',
            'genres' => 'array',
            'price' => 'decimal:2',
            'average_rating' => 'decimal:2',
            'file_size' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    // sauvegarde la progression de lecture.
    public function readingProgress(): HasMany
    {
        return $this->hasMany(ReadingProgress::class);
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class);
    }

    public function recommendationSources(): HasMany
    {
        return $this->hasMany(Recommendation::class, 'based_on_book_id');
    }

    public function readingHistory(): HasMany
    {
        return $this->hasMany(ReadingHistory::class);
    }

    public function isPdf(): bool
    {
        return $this->file_format === 'pdf';
    }

    public function isEpub(): bool
    {
        return $this->file_format === 'epub';
    }

    public function hasRemoteFile(): bool
    {
        return filter_var($this->fichier_path, FILTER_VALIDATE_URL) !== false;
    }

    public function getStorageDisk(): string
    {
        return $this->file_disk ?: (string) config('lectura.book_upload_disk', 'public');
    }

    public function resolveReaderPath(): ?string
    {
        if (! $this->fichier_path || $this->hasRemoteFile()) {
            return null;
        }

        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $this->fichier_path);

        $candidates = [
            $path,
            base_path($path),
            public_path($path),
            storage_path('app' . DIRECTORY_SEPARATOR . $path),
            storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $path),
            storage_path('app' . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . $path),
        ];

        if (Storage::disk('public')->exists($this->fichier_path)) {
            $candidates[] = Storage::disk('public')->path($this->fichier_path);
        }

        foreach (array_unique($candidates) as $candidate) {
            if ($candidate && is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    public function getReaderMimeType(): string
    {
        if ($this->file_mime_type) {
            return $this->file_mime_type;
        }

        return match ($this->file_format) {
            'pdf' => 'application/pdf',
            'epub' => 'application/epub+zip',
            default => 'application/octet-stream',
        };
    }

    public function getReaderAssetUrlAttribute(): string
    {
        if ($this->hasRemoteFile()) {
            return $this->fichier_path;
        }

        return route('reader.asset', $this, false);
    }

    public function getDisplayCoverUrlAttribute(): string
    {
        if ($this->cover_image) {
            if (filter_var($this->cover_image, FILTER_VALIDATE_URL)) {
                return $this->cover_image;
            }

            $coverPath = ltrim(str_replace('\\', '/', (string) $this->cover_image), '/');

            if (str_starts_with($coverPath, 'storage/') || str_starts_with($coverPath, 'images/')) {
                return '/' . $coverPath;
            }

            if (Storage::disk('public')->exists($coverPath)) {
                return '/storage/' . $coverPath;
            }

            return '/' . $coverPath;
        }

        $fallbacks = [
            '/images/covers/cover-atlas.svg',
            '/images/covers/cover-nocturne.svg',
            '/images/covers/cover-aurora.svg',
            '/images/covers/cover-signal.svg',
        ];

        return $fallbacks[$this->id % count($fallbacks)];
    }

    public function getUserProgress(?int $userId = null): ?ReadingProgress
    {
        $userId ??= auth()->id();

        if (! $userId) {
            return null;
        }

        return $this->readingProgress()
            ->where('user_id', $userId)
            ->first();
    }

    public function normalizedGenres(): array
    {
        return collect($this->genres ?? [])
            ->map(fn ($genre) => trim(mb_strtolower((string) $genre)))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
