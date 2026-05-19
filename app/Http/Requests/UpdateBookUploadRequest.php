<?php

namespace App\Http\Requests;

use App\Models\Book;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class UpdateBookUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Book|null $book */
        $book = $this->route('book');

        return [
            'title' => ['required', 'string', 'max:255'],
            'author_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'language' => ['required', 'string', 'max:10'],
            'genres' => ['nullable', 'string', 'max:500'],
            'isbn' => ['nullable', 'string', 'max:20', Rule::unique('books', 'isbn')->ignore($book?->id)],
            'page_count' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'published_at' => ['nullable', 'date'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'is_published' => ['nullable', 'boolean'],
            'cover_file' => [
                'nullable',
                File::image()->max(5120),
            ],
            'book_file' => [
                'nullable',
                File::types(['pdf', 'epub'])->max(51200),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'cover_file.image' => 'La couverture doit etre une image valide.',
            'cover_file.max' => 'L image de couverture ne doit pas depasser 5 Mo.',
            'book_file.max' => 'Le fichier ne doit pas depasser 50 Mo.',
            'isbn.unique' => 'Cet ISBN existe deja dans la bibliotheque.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => trim((string) $this->input('title')),
            'author_name' => trim((string) $this->input('author_name')),
            'description' => trim((string) $this->input('description')),
            'language' => strtolower(trim((string) ($this->input('language') ?: 'fr'))),
            'genres' => trim((string) $this->input('genres')) ?: null,
            'isbn' => trim((string) $this->input('isbn')) ?: null,
            'is_published' => $this->has('is_published') ? $this->boolean('is_published') : false,
        ]);
    }

    public function after(): array
    {
        return [
            function ($validator): void {
                $file = $this->file('book_file');

                if (! $file || ! $file->isValid()) {
                    return;
                }

                $extension = strtolower((string) $file->getClientOriginalExtension());

                if (! in_array($extension, ['pdf', 'epub'], true)) {
                    $validator->errors()->add('book_file', 'Seuls les fichiers PDF et EPUB sont autorises.');
                }
            },
        ];
    }
}
