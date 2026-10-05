<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Throwable;

class PublicMediaController extends Controller
{
    public function show(string $directory, string $filename): Response|\Symfony\Component\HttpFoundation\StreamedResponse
    {
        // The route only accepts our CMS image folders and uploaded image filenames.
        // No user-controlled bucket, arbitrary paths, or external URLs are supported.
        $path = $directory.'/'.$filename;
        $disk = Storage::disk(config('filesystems.default'));

        try {
            if (! $disk->exists($path)) {
                abort(404);
            }

            $stream = $disk->readStream($path);
        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            report($exception);
            abort(502, 'Media sementara tidak dapat diakses.');
        }

        if (! is_resource($stream)) {
            abort(404);
        }

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $mime = match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
        };

        return response()->stream(static function () use ($stream): void {
            try {
                fpassthru($stream);
            } finally {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
