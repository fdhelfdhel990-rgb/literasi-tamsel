<?php

namespace Tests\Concerns;

use Illuminate\Http\UploadedFile;

trait CreatesTestImages
{
    protected function fakeImage(string $name): UploadedFile
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC', true);

        return UploadedFile::fake()->create($name, $png);
    }
}