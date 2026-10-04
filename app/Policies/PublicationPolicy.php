<?php

namespace App\Policies;

use App\Models\Publication;
use App\Models\User;

class PublicationPolicy
{
    public function viewAny(User $user): bool { return $user->hasPermission('publications.manage'); }
    public function create(User $user): bool { return $user->hasPermission('publications.manage'); }
    public function update(User $user, Publication $publication): bool { return $user->hasPermission('publications.manage'); }
    public function delete(User $user, Publication $publication): bool { return $user->hasPermission('publications.manage'); }
}