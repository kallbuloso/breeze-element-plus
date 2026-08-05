<?php

declare(strict_types=1);

namespace Kallbuloso\BreezeElementPlus\Tests\Feature\Console;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kallbuloso\BreezeElementPlus\Tests\TestCase;

class MakeCrudCommandTenancyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('tenant_products');
        Schema::dropIfExists('tenants');
        Schema::create('tenants', fn (Blueprint $table) => $table->id());
        Schema::create('tenant_products', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->string('name');
            $table->timestamps();
        });
        config(['breeze-element-plus.tenancy' => 'multi']);
    }

    public function test_it_generates_tenant_scoped_artifacts_without_tenant_input(): void
    {
        $this->artisan('breeze-element-plus:crud', [
            'table' => 'tenant_products',
            '--force' => true,
            '--skip-format' => true,
        ])->assertExitCode(0);

        $model = (string) file_get_contents(app_path('Models/TenantProduct.php'));
        $request = (string) file_get_contents(app_path('Http/Requests/TenantProductRequest.php'));
        $form = (string) file_get_contents(resource_path('js/Pages/TenantProducts/_Form.vue'));
        $factory = (string) file_get_contents(database_path('factories/TenantProductFactory.php'));

        $this->assertStringContainsString('use App\\Traits\\BelongsToTenant;', $model);
        $this->assertStringContainsString('use HasFactory, BelongsToTenant;', $model);
        $this->assertStringNotContainsString("'tenant_id',", $request);
        $this->assertStringNotContainsString('tenant_id:', $form);
        $this->assertStringNotContainsString('...(props.tenantProduct ?? {})', $form);
        $this->assertStringContainsString('Tenant::factory()', $factory);
    }
}
