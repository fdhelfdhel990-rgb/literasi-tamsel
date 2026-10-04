<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\InitialSuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class InitialSuperAdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_initial_super_admin_uses_environment_and_never_resets_existing_password(): void
    {
        $name = 'Initial Owner '.Str::uuid();
        $email = Str::uuid().'@example.test';
        $password = Str::random(48);
        Config::set('initial_admin.name', $name);
        Config::set('initial_admin.email', $email);
        Config::set('initial_admin.password', $password);

        (new InitialSuperAdminSeeder())->run();
        $account = User::query()->where('email', $email)->firstOrFail();
        $firstHash = $account->password;
        $this->assertSame(User::ROLE_SUPER_ADMIN, $account->role);
        $this->assertNotEmpty($account->username);
        $this->assertTrue(Hash::check($password, $account->password));
        $this->post(route('admin.login.store'), [
            'identifier' => $email,
            'password' => $password,
        ])->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($account);
        $this->get(route('admin.dashboard'))->assertOk()->assertSee('Kelola Admin');

        Config::set('initial_admin.name', 'Changed Name');
        Config::set('initial_admin.password', Str::random(48));
        (new InitialSuperAdminSeeder())->run();

        $account->refresh();
        $this->assertSame($name, $account->name);
        $this->assertSame($firstHash, $account->password);
    }
}
