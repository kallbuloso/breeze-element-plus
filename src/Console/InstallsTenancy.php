<?php

namespace Kallbuloso\BreezeElementPlus\Console;

use Illuminate\Filesystem\Filesystem;

trait InstallsTenancy
{
    protected function isMultiTenant(): bool
    {
        return $this->tenancy === 'multi';
    }

    protected function installTenancyScaffolding(): void
    {
        if (! $this->isMultiTenant()) {
            return;
        }

        $files = new Filesystem;
        $stubs = __DIR__.'/../../stubs/tenancy';

        $files->copyDirectory($stubs.'/app/Models', app_path('Models'));
        $files->copyDirectory($stubs.'/app/Scopes', app_path('Scopes'));
        $files->copyDirectory($stubs.'/app/Traits', app_path('Traits'));
        $files->copyDirectory($stubs.'/database/factories', database_path('factories'));
        $files->copyDirectory($stubs.'/database/migrations', database_path('migrations'));
        $files->copyDirectory($stubs.'/database/seeders', database_path('seeders'));
        $files->copyDirectory($stubs.'/auth/app/Models', app_path('Models'));
        $files->copyDirectory($stubs.'/auth/database/factories', database_path('factories'));
        $files->copyDirectory($stubs.'/auth/database/migrations', database_path('migrations'));
        $files->copyDirectory($stubs.'/auth/database/seeders', database_path('seeders'));
        $authStack = $this->argument('stack') === 'vue' ? 'inertia-common' : 'api';

        $files->copyDirectory(
            $stubs.'/'.$authStack.'/app/Http/Controllers/Auth',
            app_path('Http/Controllers/Auth'),
        );
    }
}
