<?php

namespace App\Http\Requests\Admin;

use App\Models\Publication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PublicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $publication = $this->route('publication');

        return $this->user()?->can($publication ? 'update' : 'create', $publication ?: Publication::class) ?? false;
    }

    public function rules(): array
    {
        $publication = $this->route('publication');
        $published = $this->input('status') === 'published';

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('publications', 'slug')->ignore($publication?->id)],
            'category' => ['required', 'string', 'max:80'],
            'published_at' => [Rule::requiredIf($published), 'nullable', 'date'],
            'excerpt' => ['required', 'string', 'max:1000'],
            'content' => ['required', 'string', 'max:100000'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }
}