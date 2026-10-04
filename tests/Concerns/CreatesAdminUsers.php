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

        $id = (string) Str::uuid();

        return User::query()->create([
            'name' => 'Test Admin',
            'email' => $id.'@example.test',
            'username' => 'admin_'.$id,
            'password' => Hash::make($password),
            'role' => $role,
            'permissions' => $permissions,
            'is_active' => $active,
        ]);
    }
}
