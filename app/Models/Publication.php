<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Publication extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'category', 'published_at', 'excerpt', 'content', 'featured_image_path', 'status', 'created_by'];

    protected function casts(): array
    {
        return ['published_at' => 'date'];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        $path = $this->featured_image_path;
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
            'date' => $this->published_at?->locale('id')->translatedFormat('d F Y') ?? '',
            'type' => $this->category,
            'image' => $this->featured_image_url,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
        ];
    }
}