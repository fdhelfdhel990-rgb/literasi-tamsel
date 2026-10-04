<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class AdminAccountManagementTest extends TestCase
{
    use CreatesAdminUsers;
    use RefreshDatabase;

    public function test_super_admin_can_create_limit_and_disable_admin_accounts(): void
    {
        $superAdmin = $this->createAdmin(User::ROLE_SUPER_ADMIN);
        $this->actingAs($superAdmin);
        $subAdminPassword = Str::random(48);

        $this->post(route('admin.users.store'), [
            'name' => 'Assigned Sub Admin',
            'email' => 'assigned-sub@example.test',
            'username' => 'assigned_sub',
            'password' => $subAdminPassword,
            'password_confirmation' => $subAdminPassword,
            'role' => User::ROLE_SUB_ADMIN,
            'permissions' => ['books.manage'],
            'is_active' => '1',
        ])->assertRedirect(route('admin.users.index'));

        $subAdmin = User::query()->where('email', 'assigned-sub@example.test')->firstOrFail();
        $this->assertSame(User::ROLE_SUB_ADMIN, $subAdmin->role);
        $this->assertSame('assigned_sub', $subAdmin->username);
        $this->assertSame(['books.manage'], $subAdmin->permissions);
        $this->assertTrue(Hash::check($subAdminPassword, $subAdmin->password));
        $this->assertNotSame($subAdminPassword, $subAdmin->password);
        $escalationPassword = Str::random(48);

        $this->post(route('admin.users.store'), [
            'name' => 'Escalation Attempt',
            'email' => 'escalation@example.test',
            'username' => 'escalation',
            'password' => $escalationPassword,
            'password_confirmation' => $escalationPassword,
            'role' => User::ROLE_SUPER_ADMIN,
            'is_active' => '1',
        ])->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email' => 'escalation@example.test']);

        $this->delete(route('admin.users.destroy', $subAdmin))->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['id' => $subAdmin->id, 'is_active' => false]);
    }

    public function test_super_admin_transfer_changes_roles_in_one_request_and_ends_current_session(): void
    {
        $ownerPassword = Str::random(48);
        $owner = $this->createAdmin(User::ROLE_SUPER_ADMIN, password: $ownerPassword);
        $target = $this->createAdmin(User::ROLE_ADMIN);

        $this->actingAs($owner)->post(route('admin.users.transfer'), [
            'target_user_id' => $target->id,
            'confirmation' => 'TRANSFER SUPER ADMIN',
            'current_password' => $ownerPassword,
        ])->assertRedirect(route('admin.login'));

        $this->assertDatabaseHas('users', ['id' => $target->id, 'role' => User::ROLE_SUPER_ADMIN]);
        $this->assertDatabaseHas('users', ['id' => $owner->id, 'role' => User::ROLE_ADMIN]);
        $this->assertSame(1, User::query()->where('role', User::ROLE_SUPER_ADMIN)->count());
        $this->assertGuest();
    }

    public function test_sub_admin_cannot_transfer_super_admin(): void
    {
        $subAdmin = $this->createAdmin(User::ROLE_SUB_ADMIN);
        $target = $this->createAdmin(User::ROLE_ADMIN);

        $this->actingAs($subAdmin)->post(route('admin.users.transfer'), [
            'target_user_id' => $target->id,
            'confirmation' => 'TRANSFER SUPER ADMIN',
            'current_password' => Str::random(48),
        ])->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $target->id, 'role' => User::ROLE_ADMIN]);
    }
}
