<?php
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
Artisan::command('inspire', fn () => $this->comment('Terus bergerak bersama literasi.'));
Artisan::command('admin:reset-credentials {identifier : Email atau username akun admin}', function (string $identifier): int {
    $normalized = mb_strtolower(trim($identifier));
    $user = User::query()
        ->where(function ($query) use ($normalized): void {
            $query->whereRaw('LOWER(email) = ?', [$normalized])
                ->orWhereRaw('LOWER(username) = ?', [$normalized]);
        })
        ->first();

    if (! $user || ! in_array($user->role, [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN, User::ROLE_SUB_ADMIN], true)) {
        $this->error('Akun admin tidak ditemukan.');

        return 1;
    }

    if (! $this->confirm("Reset password untuk {$user->email}?", false)) {
        $this->warn('Reset dibatalkan.');

        return 1;
    }

    $password = (string) $this->secret('Masukkan password baru (minimal 12 karakter)');
    $confirmation = (string) $this->secret('Ulangi password baru');

    if ($password !== $confirmation || mb_strlen($password) < 12) {
        $this->error('Password tidak cocok atau kurang dari 12 karakter.');

        return 1;
    }

    $user->forceFill(['password' => Hash::make($password), 'is_active' => true])->save();
    $this->info('Kredensial admin berhasil diperbarui.');

    return 0;
});
