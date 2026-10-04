<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, User $target): bool
    {
        return $user->isSuperAdmin()
            && $target->role !== User::ROLE_SUPER_ADMIN
            && $target->id !== $user->id;
    }

    public function delete(User $user, User $target): bool
    {
        return $user->isSuperAdmin()
            && $target->role !== User::ROLE_SUPER_ADMIN
            && $target->id !== $user->id;
    }

    public function transferSuperAdmin(User $user, User $target): bool
    {
        return $user->isSuperAdmin()
            && $target->is_active
            && $target->id !== $user->id
            && $target->role !== User::ROLE_SUPER_ADMIN;
    }
}