<?php

namespace App\Facades;

use App\Services\ToastService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Services\ToastService success(string $message, int $duration = 3500, ?string $title = null)
 * @method static \App\Services\ToastService error(string $message, int $duration = 3500, ?string $title = null)
 * @method static \App\Services\ToastService warning(string $message, int $duration = 3500, ?string $title = null)
 * @method static \App\Services\ToastService info(string $message, int $duration = 3500, ?string $title = null)
 * @method static \App\Services\ToastService validation(array|string $targets)
 * @method static array all()
 *
 * @see ToastService
 */
class Toast extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ToastService::class;
    }
}
