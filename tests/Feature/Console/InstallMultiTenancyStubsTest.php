<?php

declare(strict_types=1);

namespace Kallbuloso\BreezeElementPlus\Tests\Feature\Console;

use Illuminate\Filesystem\Filesystem;
use Kallbuloso\BreezeElementPlus\Console\InstallCommand;
use Kallbuloso\BreezeElementPlus\Tests\TestCase;
use Symfony\Component\Console\Input\ArrayInput;

class InstallMultiTenancyStubsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        (new Filesystem)->delete([
            app_path('Models/Tenant.php'),
            app_path('Models/User.php'),
            app_path('Scopes/TenantScope.php'),
            app_path('Traits/BelongsToTenant.php'),
            database_path('factories/TenantFactory.php'),
            database_path('factories/UserFactory.php'),
            database_path('migrations/0000_01_01_000000_create_tenants_table.php'),
            database_path('migrations/0001_01_01_000000_create_users_table.php'),
            database_path('seeders/DatabaseSeeder.php'),
            config_path('breeze-element-plus.php'),
        ]);
    }

    public function test_multi_tenancy_installs_the_tenant_kernel_and_auth_variants(): void
    {
        $command = new class extends InstallCommand
        {
            public function installMultiTenantAuthForTest(): void
            {
                $this->tenancy = 'multi';
                $this->input = new ArrayInput(['stack' => 'api'], $this->getDefinition());
                $this->installAuthScaffolding();
            }
        };

        $command->installMultiTenantAuthForTest();

        $this->assertFileExists(app_path('Models/Tenant.php'));
        $this->assertFileExists(app_path('Scopes/TenantScope.php'));
        $this->assertFileExists(app_path('Traits/BelongsToTenant.php'));
        $this->assertStringContainsString('$table->unique([\'email\', \'tenant_id\']);', (string) file_get_contents(database_path('migrations/0001_01_01_000000_create_users_table.php')));
        $this->assertStringContainsString('function tenant(): BelongsTo', (string) file_get_contents(app_path('Models/User.php')));
        $this->assertStringContainsString("Tenant::factory()->create", (string) file_get_contents(database_path('seeders/DatabaseSeeder.php')));
        $this->assertStringContainsString('Tenant::create', (string) file_get_contents(app_path('Http/Controllers/Auth/RegisteredUserController.php')));
        $this->assertSame('multi', (require config_path('breeze-element-plus.php'))['tenancy']);
    }

    public function test_single_tenancy_does_not_install_tenant_artifacts(): void
    {
        $command = new class extends InstallCommand
        {
            public function installSingleTenantAuthForTest(): void
            {
                $this->input = new ArrayInput(['stack' => 'api'], $this->getDefinition());
                $this->installAuthScaffolding();
            }
        };

        $command->installSingleTenantAuthForTest();

        $this->assertFileDoesNotExist(app_path('Models/Tenant.php'));
        $this->assertFileDoesNotExist(app_path('Scopes/TenantScope.php'));
        $this->assertFileDoesNotExist(app_path('Traits/BelongsToTenant.php'));
        $this->assertStringNotContainsString('tenant_id', (string) file_get_contents(database_path('migrations/0001_01_01_000000_create_users_table.php')));
        $this->assertSame('single', (require config_path('breeze-element-plus.php'))['tenancy']);
    }
}
