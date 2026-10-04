<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class InitialSuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $name = trim((string) env('INITIAL_ADMIN_NAME', ''));
        $email = mb_strtolower(trim((string) env('INITIAL_ADMIN_EMAIL', '')));
        $password = (string) env('INITIAL_ADMIN_PASSWORD', '');

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

            User::query()->create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => User::ROLE_SUPER_ADMIN,
                'permissions' => null,
                'is_active' => true,
            ]);
        });
    }
}