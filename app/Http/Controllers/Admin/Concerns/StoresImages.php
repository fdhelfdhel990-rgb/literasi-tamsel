<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait StoresImages
{
    protected function storeImage(UploadedFile $image, string $directory): string
    {
        return $image->store($directory, config('filesystems.default'));
    }

    protected function deleteStoredImage(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'legacy:')) {
            Storage::disk(config('filesystems.default'))->delete($path);
        }
    }
}