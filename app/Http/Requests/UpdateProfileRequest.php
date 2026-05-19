<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        /** @var User $user */
        $user = $this->user();

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'avatar' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1200'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'theme' => ['required', Rule::in(['dark', 'light', 'sepia'])],
            'font_size' => ['required', Rule::in(['small', 'medium', 'large'])],
            'line_spacing' => ['required', Rule::in(['compact', 'comfortable', 'wide'])],
            'page_flip_enabled' => ['nullable', 'boolean'],
            'immersive_mode_default' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'email' => strtolower(trim((string) $this->input('email'))),
            'avatar' => trim((string) $this->input('avatar')) ?: null,
            'bio' => trim((string) $this->input('bio')) ?: null,
            'page_flip_enabled' => $this->has('page_flip_enabled') ? $this->boolean('page_flip_enabled') : false,
            'immersive_mode_default' => $this->has('immersive_mode_default') ? $this->boolean('immersive_mode_default') : false,
        ]);
    }
}
