<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\InitialSuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class InitialSuperAdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_initial_super_admin_uses_environment_and_never_resets_existing_password(): void
    {
        $repository = Env::getRepository();
        $original = [];
        foreach (['INITIAL_ADMIN_NAME', 'INITIAL_ADMIN_EMAIL', 'INITIAL_ADMIN_PASSWORD'] as $key) {
            $original[$key] = $repository->get($key);
        }

        $name = 'Initial Owner '.Str::uuid();
        $email = Str::uuid().'@example.test';
        $password = Str::random(48);
        $repository->set('INITIAL_ADMIN_NAME', $name);
        $repository->set('INITIAL_ADMIN_EMAIL', $email);
        $repository->set('INITIAL_ADMIN_PASSWORD', $password);

        try {
            (new InitialSuperAdminSeeder())->run();
            $account = User::query()->where('email', $email)->firstOrFail();
            $firstHash = $account->password;
            $this->assertSame(User::ROLE_SUPER_ADMIN, $account->role);
            $this->assertTrue(Hash::check($password, $account->password));
            $this->post(route('admin.login.store'), [
                'email' => $email,
                'password' => $password,
            ])->assertRedirect(route('admin.dashboard'));
            $this->assertAuthenticatedAs($account);
            $this->get(route('admin.dashboard'))->assertOk()->assertSee('Kelola Admin');

            $repository->set('INITIAL_ADMIN_NAME', 'Changed Name');
            $repository->set('INITIAL_ADMIN_PASSWORD', Str::random(48));
            (new InitialSuperAdminSeeder())->run();

            $account->refresh();
            $this->assertSame($name, $account->name);
            $this->assertSame($firstHash, $account->password);
        } finally {
            foreach ($original as $key => $value) {
                if ($value === null) {
                    $repository->clear($key);
                } else {
                    $repository->set($key, $value);
                }
            }
        }
    }
}