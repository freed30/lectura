<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\ReadingHistory;
use App\Models\ReadingProgress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReadingProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_saves_the_current_page_in_reading_progress(): void
    {
        $user = User::factory()->create();
        $book = $this->createBook('pdf');

        $response = $this->actingAs($user)->postJson(route('reader.progress.store', $book), [
            'current_page' => 12,
            'total_pages' => 120,
            'progress_percent' => 10,
            'is_finished' => false,
        ]);

        $response->assertOk()
            ->assertJsonPath('saved', true)
            ->assertJsonPath('progress.current_page', 12);

        $progress = ReadingProgress::query()
            ->where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->first();

        $this->assertNotNull($progress);
        $this->assertSame(12, $progress->current_page);
        $this->assertSame(120, $progress->total_pages);
        $this->assertSame('10.00', (string) $progress->progress_percent);
        $this->assertFalse($progress->is_finished);
        $this->assertNotNull($progress->last_read_at);

        $history = ReadingHistory::query()
            ->where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->first();

        $this->assertNotNull($history);
        $this->assertNotNull($history->started_at);
        $this->assertNotNull($history->last_opened_at);
        $this->assertSame('10.00', (string) $history->last_progress_percent);
    }

    public function test_it_calculates_progress_from_the_current_page_when_percent_is_missing(): void
    {
        $user = User::factory()->create();
        $book = $this->createBook('pdf');

        $this->actingAs($user)->postJson(route('reader.progress.store', $book), [
            'current_page' => 45,
            'total_pages' => 90,
        ])->assertOk();

        $progress = ReadingProgress::query()
            ->where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->first();

        $this->assertNotNull($progress);
        $this->assertSame(45, $progress->current_page);
        $this->assertSame(90, $progress->total_pages);
        $this->assertSame('50.00', (string) $progress->progress_percent);
    }

    public function test_it_passes_saved_progress_to_the_reader_view_for_resume(): void
    {
        $user = User::factory()->create();
        $book = $this->createBook('epub');

        ReadingProgress::query()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'current_page' => 18,
            'current_location' => 'epubcfi(/6/14[chapter1]!/4/2/8)',
            'total_pages' => 42,
            'progress_percent' => 42.86,
            'is_finished' => false,
            'last_read_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('reader.show', $book));

        $response->assertOk()
            ->assertViewHas('progress', function ($progress) {
                return $progress instanceof ReadingProgress
                    && $progress->current_page === 18
                    && $progress->current_location === 'epubcfi(/6/14[chapter1]!/4/2/8)';
            })
            ->assertSee('"current_page":18', false)
            ->assertSee('"current_location":"epubcfi(\/6\/14[chapter1]!\/4\/2\/8)"', false);
    }

    protected function createBook(string $format): Book
    {
        $author = Author::query()->create([
            'name' => 'Auteur test',
        ]);

        return Book::query()->create([
            'author_id' => $author->id,
            'title' => 'Livre de test ' . strtoupper($format),
            'slug' => 'livre-test-' . $format . '-' . Str::lower(Str::random(6)),
            'description' => 'Livre de test pour la progression.',
            'language' => 'fr',
            'fichier_path' => 'https://example.com/book.' . $format,
            'file_format' => $format,
            'page_count' => 120,
            'price' => 0,
            'is_published' => true,
        ]);
    }
}
