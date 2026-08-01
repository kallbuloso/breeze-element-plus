<?php

declare(strict_types=1);

namespace Kallbuloso\BreezeElementPlus\Tests\Feature\Console;

use Kallbuloso\BreezeElementPlus\Tests\TestCase;

class MakeCrudCommandTest extends TestCase
{
    public function test_it_generates_a_vue_crud_and_integrates_it_idempotently(): void
    {
        $this->artisan('breeze-element-plus:crud', [
            'table' => 'products',
            '--group' => 'catalog',
            '--group-label' => 'Catálogo',
            '--singular' => 'Produto',
            '--plural' => 'Produtos',
            '--icon' => 'ri:box-3-line',
            '--skip-format' => true,
        ])->assertExitCode(0);

        $this->assertFileExists(app_path('Models/Product.php'));
        $this->assertFileExists(app_path('Http/Controllers/ProductController.php'));
        $this->assertFileExists(app_path('Http/Requests/ProductRequest.php'));
        $this->assertFileExists(resource_path('js/Pages/Products/Index.vue'));
        $this->assertFileExists(resource_path('js/Pages/Products/_Form.vue'));
        $this->assertStringContainsString("'price' => 'decimal:2'", (string) file_get_contents(app_path('Models/Product.php')));
        $this->assertStringContainsString('Route::resource(\'products\', ProductController::class);', (string) file_get_contents(base_path('routes/web.php')));
        $this->assertStringContainsString("key: 'catalog'", (string) file_get_contents(resource_path('js/Configs/nav.js')));
        $this->assertStringContainsString("route: 'products.index'", (string) file_get_contents(resource_path('js/Configs/nav.js')));

        $this->artisan('breeze-element-plus:crud', [
            'table' => 'products',
            '--group' => 'catalog',
            '--group-label' => 'Catálogo',
            '--singular' => 'Produto',
            '--plural' => 'Produtos',
            '--icon' => 'ri:box-3-line',
            '--skip-format' => true,
        ])->assertExitCode(0);

        $routes = (string) file_get_contents(base_path('routes/web.php'));
        $nav = (string) file_get_contents(resource_path('js/Configs/nav.js'));
        $this->assertSame(1, substr_count($routes, "Route::resource('products', ProductController::class);"));
        $this->assertSame(1, substr_count($nav, "route: 'products.index'"));
    }

    public function test_dry_run_does_not_write_files(): void
    {
        $this->artisan('breeze-element-plus:crud', [
            'table' => 'products',
            '--dry-run' => true,
            '--skip-format' => true,
        ])->assertExitCode(0);

        $this->assertFileDoesNotExist(app_path('Models/Product.php'));
    }

    public function test_it_refuses_to_run_without_the_vue_stack_prerequisites(): void
    {
        unlink(resource_path('js/Layouts/AuthenticatedLayout.vue'));

        $this->artisan('breeze-element-plus:crud', [
            'table' => 'products',
            '--skip-format' => true,
        ])->assertExitCode(1);

        $this->assertFileDoesNotExist(app_path('Models/Product.php'));
    }

    public function test_it_rejects_an_unsafe_resource_uri_before_writing_files(): void
    {
        $this->artisan('breeze-element-plus:crud', [
            'table' => 'products',
            '--route' => '../../outside',
            '--skip-format' => true,
        ])->assertExitCode(1);

        $this->assertFileDoesNotExist(app_path('Models/Product.php'));
    }

    public function test_it_uses_a_custom_resource_uri_as_the_route_name_base(): void
    {
        $this->artisan('breeze-element-plus:crud', [
            'table' => 'products',
            '--route' => 'catalog/products',
            '--skip-format' => true,
        ])->assertExitCode(0);

        $routes = (string) file_get_contents(base_path('routes/web.php'));
        $index = (string) file_get_contents(resource_path('js/Pages/Products/Index.vue'));

        $this->assertStringContainsString("Route::resource('catalog/products', ProductController::class);", $routes);
        $this->assertStringContainsString("route('catalog.products.index')", $index);
    }

    public function test_generated_mutating_endpoints_require_the_owner_user(): void
    {
        $this->artisan('breeze-element-plus:crud', [
            'table' => 'products',
            '--skip-format' => true,
        ])->assertExitCode(0);

        $controller = (string) file_get_contents(app_path('Http/Controllers/ProductController.php'));
        $request = (string) file_get_contents(app_path('Http/Requests/ProductRequest.php'));

        $this->assertStringContainsString('ensureOwner', $controller);
        $this->assertStringContainsString('is_owner', $request);
    }
}
