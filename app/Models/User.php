<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_SUB_ADMIN = 'sub_admin';

    public const CONTENT_PERMISSIONS = [
        'publications.manage',
        'books.manage',
        'partners.manage',
        'site_content.manage',
        'join_cards.manage',
    ];

    protected $fillable = ['name', 'email', 'password', 'role', 'permissions', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function hasPermission(string $permission): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->role === self::ROLE_SUPER_ADMIN) {
            return true;
        }

        if ($this->role === self::ROLE_ADMIN) {
            return in_array($permission, self::CONTENT_PERMISSIONS, true);
        }

        return $this->role === self::ROLE_SUB_ADMIN
            && in_array($permission, $this->permissions ?? [], true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->is_active && $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function publications(): HasMany
    {
        return $this->hasMany(Publication::class, 'created_by');
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class, 'created_by');
    }

    public function mediaPartners(): HasMany
    {
        return $this->hasMany(MediaPartner::class, 'created_by');
    }
}