<?php

namespace App\Policies;

use App\Models\MediaPartner;
use App\Models\User;

class MediaPartnerPolicy
{
    public function viewAny(User $user): bool { return $user->hasPermission('partners.manage'); }
    public function create(User $user): bool { return $user->hasPermission('partners.manage'); }
    public function update(User $user, MediaPartner $partner): bool { return $user->hasPermission('partners.manage'); }
    public function delete(User $user, MediaPartner $partner): bool { return $user->hasPermission('partners.manage'); }
}