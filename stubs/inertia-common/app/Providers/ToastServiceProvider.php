<?php

namespace App\Providers;

use App\Services\ToastService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\ServiceProvider;

class ToastServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ToastService::class, fn () => new ToastService);
    }

    public function boot(): void
    {
        RedirectResponse::macro('toast', function (string $message, string $type = 'success', int $duration = 3500, ?string $title = null) {
            /** @var ToastService $toast */
            $toast = resolve(ToastService::class);
            $toast->{$type}($message, $duration, $title);

            return $this;
        });
    }
}
