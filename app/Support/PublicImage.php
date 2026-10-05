<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

final class PublicImage
{
    public static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'legacy:')) {
            return asset(substr($path, 7));
        }

        if (config('filesystems.default') === 'r2'
            && preg_match('~^(books|publications|partners|community)/([A-Za-z0-9_-]+\.(?:jpg|jpeg|png|webp))$~i', $path, $matches)) {
            return route('media.show', [
                'directory' => $matches[1],
                'filename' => $matches[2],
            ]);
        }

        return Storage::disk(config('filesystems.default'))->url($path);
    }
}
