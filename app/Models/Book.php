<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'author', 'publisher', 'genre', 'description', 'isbn', 'cover_path', 'is_published', 'created_by'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getCoverUrlAttribute(): ?string
    {
        $path = $this->cover_path;
        if (! $path) {
            return null;
        }

        return str_starts_with($path, 'legacy:')
            ? asset(substr($path, 7))
            : Storage::disk(config('filesystems.default'))->url($path);
    }

    public function toPublicArray(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'author' => $this->author,
            'publisher' => $this->publisher,
            'genre' => $this->genre,
            'description' => $this->description,
            'cover' => $this->cover_url,
        ];
    }
}