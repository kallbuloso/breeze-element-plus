<?php

namespace App\Providers;

use App\Services\ToastService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ToastService::class, fn () => new ToastService);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        RedirectResponse::macro('toast', function (string $message, string $type = 'success', int $duration = 3500, ?string $title = null) {
            /** @var ToastService $toast */
            $toast = resolve(ToastService::class);
            $toast->{$type}($message, $duration, $title);

            return $this;
        });
    }
}
