<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_routes_redirect_guests_to_login(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
    }

    public function test_active_admin_can_login_remain_authenticated_and_logout(): void
    {
        $password = Str::random(48);
        $user = User::query()->create([
            'name' => 'Content Admin',
            'email' => 'content-admin@example.test',
            'password' => Hash::make($password),
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $this->get(route('admin.login'))->assertOk()->assertSee('Ingat saya');
        $this->post(route('admin.login.store'), [
            'email' => $user->email,
            'password' => $password,
            'remember' => '1',
        ])->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->get(route('admin.dashboard'))->assertOk()->assertSee('Koleksi Buku');
        $this->post(route('admin.logout'))->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_inactive_admin_cannot_authenticate(): void
    {
        $password = Str::random(48);
        $user = User::query()->create([
            'name' => 'Disabled Admin',
            'email' => 'disabled-admin@example.test',
            'password' => Hash::make($password),
            'role' => User::ROLE_ADMIN,
            'is_active' => false,
        ]);

        $this->post(route('admin.login.store'), [
            'email' => $user->email,
            'password' => $password,
        ])->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}