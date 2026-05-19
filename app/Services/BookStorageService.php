<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class BookStorageService
{
    // stockage fichier sur un disque Laravel compatible local ou heberge.
    public function storeUploadedBook(UploadedFile $file, string $extension): array
    {
        $disk = $this->resolveUploadDisk();
        $storedFilename = Str::uuid() . '.' . $extension;
        $storedPath = $file->storeAs('books', $storedFilename, $disk);

        if (! $storedPath) {
            throw new \RuntimeException('Le stockage du fichier a echoue.');
        }

        return [
            'fichier_path' => $storedPath,
            'file_disk' => $disk,
            'file_mime_type' => $this->detectMimeType($file, $extension),
            'file_size' => (int) ($file->getSize() ?? 0),
        ];
    }

    // stockage image de couverture pour l'affichage visuel des livres.
    public function storeUploadedCover(UploadedFile $file): array
    {
        $disk = $this->resolveUploadDisk();
        $extension = strtolower((string) ($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg'));
        $storedFilename = Str::uuid() . '.' . $extension;
        $storedPath = $file->storeAs('covers', $storedFilename, $disk);

        if (! $storedPath) {
            throw new \RuntimeException('Le stockage de la couverture a echoue.');
        }

        return [
            'cover_image' => $storedPath,
            'file_disk' => $disk,
        ];
    }

    // recuperation du fichier depuis le disque configure pour la lecture dans le site.
    public function exists(Book $book): bool
    {
        if (! $book->fichier_path || $book->hasRemoteFile()) {
            return false;
        }

        $disk = $book->getStorageDisk();

        if ($this->diskExists($disk) && Storage::disk($disk)->exists($book->fichier_path)) {
            return true;
        }

        return $book->resolveReaderPath() !== null;
    }

    // affichage du fichier directement dans le navigateur.
    public function responseForReader(Book $book): ?Response
    {
        if (! $book->fichier_path || $book->hasRemoteFile()) {
            return null;
        }

        $disk = $book->getStorageDisk();

        if ($this->diskExists($disk) && Storage::disk($disk)->exists($book->fichier_path)) {
            $localPath = $this->resolveLocalDiskPath($disk, $book->fichier_path);

            if ($localPath) {
                // lecture directe du vrai fichier pour aider le navigateur a afficher le PDF dans la page.
                return $this->binaryFileResponse($localPath, $book);
            }

            $stream = Storage::disk($disk)->readStream($book->fichier_path);

            if (! is_resource($stream)) {
                return null;
            }

            $headers = [
                'Content-Type' => $book->getReaderMimeType(),
                'Content-Disposition' => 'inline; filename="' . basename((string) $book->fichier_path) . '"',
                'Cache-Control' => 'public, max-age=3600',
                'Accept-Ranges' => 'bytes',
            ];

            $fileSize = $book->file_size ?: Storage::disk($disk)->size($book->fichier_path);

            if ($fileSize) {
                $headers['Content-Length'] = (string) $fileSize;
            }

            return response()->stream(function () use ($stream): void {
                fpassthru($stream);

                if (is_resource($stream)) {
                    fclose($stream);
                }
            }, 200, $headers);
        }

        $path = $book->resolveReaderPath();

        if (! $path) {
            return null;
        }

        return $this->binaryFileResponse($path, $book);
    }

    public function deleteStoredBook(?string $disk, ?string $path): void
    {
        $this->deleteStoredAsset($disk, $path);
    }

    public function deleteStoredAsset(?string $disk, ?string $path): void
    {
        if (! $path || filter_var($path, FILTER_VALIDATE_URL)) {
            return;
        }

        $disk ??= $this->resolveUploadDisk();

        if ($this->diskExists($disk) && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    protected function resolveUploadDisk(): string
    {
        $disk = (string) config('lectura.book_upload_disk', 'public');

        return $this->diskExists($disk) ? $disk : 'public';
    }

    protected function diskExists(string $disk): bool
    {
        return is_array(config("filesystems.disks.{$disk}"));
    }

    protected function detectMimeType(UploadedFile $file, string $extension): string
    {
        return (string) ($file->getMimeType() ?: match ($extension) {
            'pdf' => 'application/pdf',
            'epub' => 'application/epub+zip',
            default => 'application/octet-stream',
        });
    }

    protected function resolveLocalDiskPath(string $disk, string $path): ?string
    {
        try {
            $resolvedPath = Storage::disk($disk)->path($path);
        } catch (\Throwable) {
            return null;
        }

        return is_string($resolvedPath) && is_file($resolvedPath)
            ? $resolvedPath
            : null;
    }

    protected function binaryFileResponse(string $path, Book $book): Response
    {
        $response = response()->file($path, [
            'Content-Type' => $book->getReaderMimeType(),
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
            'Cache-Control' => 'public, max-age=3600',
        ]);

        $response->headers->set('Accept-Ranges', 'bytes');

        return $response;
    }
}
