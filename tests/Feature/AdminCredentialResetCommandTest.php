<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class AdminCredentialResetCommandTest extends TestCase
{
    use CreatesAdminUsers;
    use RefreshDatabase;

    public function test_admin_password_can_be_reset_only_after_confirmation(): void
    {
        $oldPassword = Str::random(48);
        $newPassword = 'new-secure-password-123';
        $admin = $this->createAdmin(User::ROLE_ADMIN, password: $oldPassword);

        $this->artisan('admin:reset-credentials', ['identifier' => $admin->username])
            ->expectsConfirmation("Reset password untuk {$admin->email}?", 'yes')
            ->expectsQuestion('Masukkan password baru (minimal 12 karakter)', $newPassword)
            ->expectsQuestion('Ulangi password baru', $newPassword)
            ->expectsOutput('Kredensial admin berhasil diperbarui.')
            ->assertExitCode(0);

        $admin->refresh();
        $this->assertTrue(Hash::check($newPassword, $admin->password));
        $this->assertFalse(Hash::check($oldPassword, $admin->password));
    }

    public function test_admin_password_reset_can_be_cancelled(): void
    {
        $password = Str::random(48);
        $admin = $this->createAdmin(User::ROLE_ADMIN, password: $password);
        $hash = $admin->password;

        $this->artisan('admin:reset-credentials', ['identifier' => $admin->email])
            ->expectsConfirmation("Reset password untuk {$admin->email}?", 'no')
            ->expectsOutput('Reset dibatalkan.')
            ->assertExitCode(1);

        $admin->refresh();
        $this->assertSame($hash, $admin->password);
    }
}
