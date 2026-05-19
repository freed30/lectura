<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$book = App\Models\Book::find(2);
$service = app(App\Services\BookStorageService::class);

echo 'BOOK_ID=' . $book?->id . PHP_EOL;
echo 'PATH=' . $book?->fichier_path . PHP_EOL;
echo 'DISK=' . $book?->file_disk . PHP_EOL;
echo 'MIME=' . $book?->getReaderMimeType() . PHP_EOL;
echo 'EXISTS=' . ($service->exists($book) ? 'yes' : 'no') . PHP_EOL;
echo 'ASSET=' . route('reader.asset', $book, false) . PHP_EOL;
