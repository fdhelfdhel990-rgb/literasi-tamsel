<?php

namespace Tests\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait CreatesAdminUsers
{
    protected function createAdmin(string $role = User::ROLE_ADMIN, ?array $permissions = null, bool $active = true, ?string $password = null): User
    {
        $password ??= Str::random(48);

        return User::query()->create([
            'name' => 'Test Admin',
            'email' => Str::uuid().'@example.test',
            'password' => Hash::make($password),
            'role' => $role,
            'permissions' => $permissions,
            'is_active' => $active,
        ]);
    }
}