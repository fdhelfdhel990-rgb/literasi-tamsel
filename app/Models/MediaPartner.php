<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Support\PublicImage;

class MediaPartner extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image_path', 'url', 'position', 'is_active', 'created_by'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'position' => 'integer'];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getImageUrlAttribute(): ?string
    {
        $path = $this->image_path;
        if (! $path) {
            return null;
        }

        return PublicImage::url($path);
    }

    public function toPublicArray(): array
    {
        return ['name' => $this->name, 'image' => $this->image_url, 'url' => $this->url];
    }
}