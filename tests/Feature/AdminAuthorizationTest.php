<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
{
    use CreatesAdminUsers;
    use RefreshDatabase;

    public function test_admin_can_manage_content_but_cannot_manage_admin_accounts(): void
    {
        $admin = $this->createAdmin(User::ROLE_ADMIN);

        $this->actingAs($admin)->get(route('admin.publications.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($admin)->post(route('admin.users.store'), [])->assertForbidden();
    }

    public function test_sub_admin_is_limited_to_explicitly_assigned_permissions(): void
    {
        $subAdmin = $this->createAdmin(User::ROLE_SUB_ADMIN, ['books.manage']);

        $this->actingAs($subAdmin)->get(route('admin.books.index'))->assertOk();
        $this->actingAs($subAdmin)->get(route('admin.publications.index'))->assertForbidden();
        $this->actingAs($subAdmin)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_inactive_authenticated_session_is_revoked_by_middleware(): void
    {
        $admin = $this->createAdmin(User::ROLE_ADMIN, null, false);

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }
}