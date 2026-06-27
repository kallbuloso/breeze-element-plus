<?php

namespace Kallbuloso\BreezeElementPlus\Console;

use Illuminate\Filesystem\Filesystem;

trait InstallsInertiaStacks
{
    /**
     * Install the Inertia Vue Breeze Element Plus stack.
     *
     * @return int|null
     */
    protected function installInertiaVueStack()
    {
        if (! $this->requireComposerPackages(['inertiajs/inertia-laravel:^2.0', 'laravel/sanctum:^4.0', 'tightenco/ziggy:^2.0'])) {
            return 1;
        }

        $this->updateNodePackages(function ($packages) {
            return [
                '@iconify/vue' => '^5.0.0',
                '@inertiajs/vue3' => '^2.0.0',
                '@rushstack/eslint-patch' => '^1.8.0',
                '@vitejs/plugin-vue' => '^6.0.0',
                '@vue/eslint-config-prettier' => '^10.0.0',
                '@vueuse/core' => '^13.0.0',
                'autoprefixer' => '^10.4.12',
                'axios' => '^1.7.4',
                'element-plus' => '^2.9.0',
                'eslint' => '^9.0.0',
                'eslint-config-prettier' => '^10.0.0',
                'eslint-plugin-n' => '^18.0.1',
                'eslint-plugin-prettier' => '^5.0.0',
                'eslint-plugin-vue' => '^10.0.0',
                'globals' => '^16.0.0',
                'pinia' => '^3.0.0',
                'postcss' => '^8.4.31',
                'prettier' => '^3.3.0',
                'prettier-plugin-organize-imports' => '^4.0.0',
                'sass' => '^1.80.0',
                'typescript' => '^5.6.0',
                'unplugin-auto-import' => '^19.0.0',
                'unplugin-vue-components' => '^28.0.0',
                "vite" => "^8.0.14",
                "vite-plugin-eslint2" => "^5.1.0",
                'vue' => '^3.4.0',
                'vue-tsc' => '^2.1.0',
            ] + $packages;
        });

        $this->updateNodeScripts(function ($scripts) {
            return [
                'build' => 'vite build',
                'dev' => 'vite',
                'format' => 'prettier . --write',
                'lint' => 'eslint resources/js --ext .js,.vue --fix',
                'start' => 'vite',
            ] + $scripts;
        });

        $files = new Filesystem;

        $this->installAuthScaffolding();

        $files->copyDirectory(__DIR__.'/../../stubs/inertia-common/app/Providers', app_path('Providers'));
        $files->copyDirectory(__DIR__.'/../../stubs/inertia-common/app/Http/Controllers', app_path('Http/Controllers'));
        $files->copyDirectory(__DIR__.'/../../stubs/inertia-common/app/Http/Requests', app_path('Http/Requests'));
        $files->copyDirectory(__DIR__.'/../../stubs/inertia-common/app/Http/Middleware', app_path('Http/Middleware'));
        $files->copyDirectory(__DIR__.'/../../stubs/inertia-common/app/Facades', app_path('Facades'));
        $files->copyDirectory(__DIR__.'/../../stubs/inertia-common/app/Services', app_path('Services'));

        $this->installMiddleware([
            '\App\Http\Middleware\HandleInertiaRequests::class',
            '\Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class',
        ]);

        copy(__DIR__.'/../../stubs/inertia-vue/resources/views/app.blade.php', resource_path('views/app.blade.php'));

        @unlink(resource_path('views/welcome.blade.php'));

        $files->copyDirectory(__DIR__.'/../../stubs/inertia-vue/resources/js', resource_path('js'));
        $files->copyDirectory(__DIR__.'/../../stubs/inertia-vue/resources/css', resource_path('css'));
        $files->copyDirectory(__DIR__.'/../../stubs/inertia-vue/resources/images', resource_path('images'));

        if (! $this->installTests()) {
            return 1;
        }

        if ($this->option('pest')) {
            $files->copyDirectory(__DIR__.'/../../stubs/inertia-common/pest-tests/Feature', base_path('tests/Feature'));
        } else {
            $files->copyDirectory(__DIR__.'/../../stubs/inertia-common/tests/Feature', base_path('tests/Feature'));
        }

        copy(__DIR__.'/../../stubs/inertia-common/routes/web.php', base_path('routes/web.php'));
        copy(__DIR__.'/../../stubs/inertia-common/routes/auth.php', base_path('routes/auth.php'));

        copy(__DIR__.'/../../stubs/inertia-vue/postcss.config.js', base_path('postcss.config.js'));
        copy(__DIR__.'/../../stubs/inertia-common/jsconfig.json', base_path('jsconfig.json'));
        copy(__DIR__.'/../../stubs/inertia-vue/vite.config.js', base_path('vite.config.js'));
        copy(__DIR__.'/../../stubs/inertia-vue/eslint.config.js', base_path('eslint.config.js'));
        copy(__DIR__.'/../../stubs/inertia-vue/.prettierrc', base_path('.prettierrc'));
        copy(__DIR__.'/../../stubs/inertia-vue/.prettierignore', base_path('.prettierignore'));
        copy(__DIR__.'/../../stubs/inertia-vue/.editorconfig', base_path('.editorconfig'));

        if ($this->option('ssr')) {
            $this->installInertiaVueSsrStack();
        }

        $this->components->info('Refreshing and seeding the database.');
        $this->runCommands([$this->artisanCommand('migrate:fresh --seed')]);

        $this->components->info('Installing and building Node dependencies.');

        if (file_exists(base_path('pnpm-lock.yaml'))) {
            $this->runCommands(['pnpm install', 'pnpm run lint', 'pnpm run format', 'pnpm run build']);
        } elseif (file_exists(base_path('yarn.lock'))) {
            $this->runCommands(['yarn install', 'yarn run lint', 'yarn run format', 'yarn run build']);
        } elseif (file_exists(base_path('bun.lock')) || file_exists(base_path('bun.lockb'))) {
            $this->runCommands(['bun install', 'bun run lint', 'bun run format', 'bun run build']);
        } else {
            $this->runCommands(['npm install', 'npm run lint', 'npm run format', 'npm run build']);
        }

        $this->line('');
        $this->components->info('Breeze Element Plus scaffolding installed successfully.');
    }

    protected function installInertiaVueSsrStack(): void
    {
        $this->updateNodePackages(function ($packages) {
            return [
                '@vue/server-renderer' => '^3.4.0',
            ] + $packages;
        });

        $this->replaceInFile("input: 'resources/js/app.js',", "input: 'resources/js/app.js',".PHP_EOL."            ssr: 'resources/js/ssr.js',", base_path('vite.config.js'));
        $this->configureZiggyForSsr();
        $this->replaceInFile('vite build', 'vite build && vite build --ssr', base_path('package.json'));
        $this->replaceInFile('/node_modules', '/bootstrap/ssr'.PHP_EOL.'/node_modules', base_path('.gitignore'));
    }

    protected function configureZiggyForSsr(): void
    {
        //
    }
}
