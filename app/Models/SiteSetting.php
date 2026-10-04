<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value', 'updated_by'];

    protected function casts(): array
    {
        return ['value' => 'array'];
    }

    public static function valueFor(string $key, mixed $fallback = null): mixed
    {
        return static::query()->where('key', $key)->first()?->value ?? $fallback;
    }
}