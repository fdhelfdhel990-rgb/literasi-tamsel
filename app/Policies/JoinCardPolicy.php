<?php

namespace App\Policies;

use App\Models\JoinCard;
use App\Models\User;

class JoinCardPolicy
{
    public function update(User $user, JoinCard $card): bool { return $user->hasPermission('join_cards.manage'); }
}