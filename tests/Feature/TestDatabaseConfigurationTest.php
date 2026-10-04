<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TestDatabaseConfigurationTest extends TestCase
{
    public function test_phpunit_uses_in_memory_sqlite_instead_of_the_development_mysql_database(): void
    {
        $this->assertSame('sqlite', DB::connection()->getDriverName());
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
    }
}