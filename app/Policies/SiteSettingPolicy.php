<?php

namespace App\Policies;

use App\Models\SiteSetting;
use App\Models\User;

class SiteSettingPolicy
{
    public function update(User $user, SiteSetting $setting): bool { return $user->hasPermission('site_content.manage'); }
}