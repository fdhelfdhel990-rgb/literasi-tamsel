<?php

namespace App\Http\Requests\Admin;

use App\Models\Book;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookRequest extends FormRequest
{
    public function authorize(): bool
    {
        $book = $this->route('book');

        return $this->user()?->can($book ? 'update' : 'create', $book ?: Book::class) ?? false;
    }

    public function rules(): array
    {
        $book = $this->route('book');

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('books', 'slug')->ignore($book?->id)],
            'author' => ['required', 'string', 'max:255'],
            'publisher' => ['required', 'string', 'max:255'],
            'genre' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:5000'],
            'isbn' => ['nullable', 'string', 'max:32', Rule::unique('books', 'isbn')->ignore($book?->id)],
            'is_published' => ['required', 'boolean'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }
}