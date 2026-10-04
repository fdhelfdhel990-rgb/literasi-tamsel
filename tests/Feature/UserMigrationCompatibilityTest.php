<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class UserMigrationCompatibilityTest extends TestCase
{
    use CreatesAdminUsers;
    use RefreshDatabase;

    public function test_users_table_has_nullable_unique_username_column(): void
    {
        $this->assertTrue(Schema::hasColumn('users', 'username'));

        $admin = $this->createAdmin(User::ROLE_ADMIN);
        $this->assertNotEmpty($admin->username);
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'username' => $admin->username]);
    }
}
