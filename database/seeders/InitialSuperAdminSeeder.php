<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class InitialSuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $name = trim((string) config('initial_admin.name', ''));
        $email = mb_strtolower(trim((string) config('initial_admin.email', '')));
        $password = (string) config('initial_admin.password', '');

        if ($name === '' && $email === '' && $password === '' && ! app()->environment('production')) {
            return;
        }

        if ($name === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($password) < 14) {
            throw new RuntimeException('Initial Super Admin environment configuration is incomplete or invalid.');
        }

        DB::transaction(function () use ($name, $email, $password): void {
            if (User::query()->where('email', $email)->exists()) {
                return;
            }

            if (User::query()->where('role', User::ROLE_SUPER_ADMIN)->lockForUpdate()->exists()) {
                return;
            }

            $baseUsername = Str::slug(Str::before($email, '@'), '_') ?: 'super_admin';
            $username = $baseUsername;
            $suffix = 1;
            while (User::query()->where('username', $username)->exists()) {
                $tail = '_'.$suffix++;
                $username = Str::limit($baseUsername, 80 - strlen($tail), '').$tail;
            }

            User::query()->create([
                'name' => $name,
                'email' => $email,
                'username' => $username,
                'password' => Hash::make($password),
                'role' => User::ROLE_SUPER_ADMIN,
                'permissions' => null,
                'is_active' => true,
            ]);
        });
    }
}
