<?php

declare(strict_types=1);

namespace Kallbuloso\BreezeElementPlus\Tests\Unit\Console\Tenancy;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Kallbuloso\BreezeElementPlus\Tests\TestCase;

class MultiTenancyMigrationTest extends TestCase
{
    public function test_it_creates_tenant_aware_user_schema_without_changing_password_reset_contract(): void
    {
        foreach (['sessions', 'password_reset_tokens', 'users', 'tenants'] as $table) {
            Schema::dropIfExists($table);
        }

        $tenants = require dirname(__DIR__, 4).'/stubs/tenancy/database/migrations/0000_01_01_000000_create_tenants_table.php';
        $users = require dirname(__DIR__, 4).'/stubs/tenancy/auth/database/migrations/0001_01_01_000000_create_users_table.php';
        $tenants->up();
        $users->up();

        $firstTenantId = DB::table('tenants')->insertGetId(['name' => 'First', 'created_at' => now(), 'updated_at' => now()]);
        $secondTenantId = DB::table('tenants')->insertGetId(['name' => 'Second', 'created_at' => now(), 'updated_at' => now()]);

        DB::table('users')->insert($this->user($firstTenantId, 'shared@example.com'));
        DB::table('users')->insert($this->user($secondTenantId, 'shared@example.com'));

        $this->assertTrue(Schema::hasColumn('users', 'tenant_id'));
        $this->assertSame(2, DB::table('users')->where('email', 'shared@example.com')->count());
        $this->assertFalse(Schema::hasColumn('password_reset_tokens', 'tenant_id'));
    }

    private function user(int $tenantId, string $email): array
    {
        return [
            'tenant_id' => $tenantId,
            'name' => 'User',
            'email' => $email,
            'password' => 'hashed',
            'is_owner' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
