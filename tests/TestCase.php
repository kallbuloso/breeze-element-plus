<?php

declare(strict_types=1);

namespace Kallbuloso\BreezeElementPlus\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;
use Kallbuloso\BreezeElementPlus\BreezeElementPlusServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [BreezeElementPlusServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        (new Filesystem)->delete([
            app_path('Models/Product.php'),
            app_path('Http/Controllers/ProductController.php'),
            app_path('Http/Requests/ProductRequest.php'),
            database_path('factories/ProductFactory.php'),
            database_path('seeders/ProductSeeder.php'),
        ]);
        (new Filesystem)->deleteDirectory(resource_path('js/Pages/Products'));

        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');

        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained();
            $table->timestamps();
        });

        $this->prepareVueStackFiles();
    }

    protected function prepareVueStackFiles(): void
    {
        $this->writeFixtureFile('routes/web.php', <<<'PHP'
<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
});
PHP);

        $this->writeFixtureFile('resources/js/Configs/nav.js', <<<'JS'
export const nav = [
  {
    key: 'dashboard',
    labelKey: 'navigation.dashboard',
    icon: 'ri:dashboard-line',
    features: []
  }
]
JS);

        $this->writeFixtureFile('resources/js/Layouts/AuthenticatedLayout.vue', '<template><slot /></template>');
        $this->writeFixtureFile('resources/js/locales/index.js', "export const locale = 'pt_BR'\n");
        $this->writeFixtureFile('resources/js/locales/pt_BR/message.js', "export default {\n  navigation: {}\n}\n");
    }

    protected function writeFixtureFile(string $relativePath, string $contents): void
    {
        $path = base_path($relativePath);
        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($path, $contents);
    }
}
