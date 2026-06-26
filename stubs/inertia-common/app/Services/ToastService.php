<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ViewErrorBag;

class ToastService
{
    protected array $flashedToasts = [];

    /**
     * Magic call: Toast::success('msg'), Toast::error('msg'), etc.
     * Forwards to make() with the method name as the type.
     */
    public function __call(string $method, array $args): self
    {
        array_splice($args, 1, 0, [$method]);

        $this->flashedToasts[] = $this->make(...$args);

        Session::flash('toasts', $this->flashedToasts);

        return $this;
    }

    public function validation(array|string $targets): self
    {
        Session::flash('validation_toasts', Arr::wrap($targets));

        return $this;
    }

    public function make(string $message, string $type = 'success', int $duration = 3500, ?string $title = null): array
    {
        return [
            'type' => $type,
            'text' => $message,
            'duration' => $duration,
            'title' => $title,
        ];
    }

    public function getToasts(): array
    {
        return Session::get('toasts') ?? [];
    }

    public function getValidationToasts(): array
    {
        $validationErrors = Session::get('errors', app(ViewErrorBag::class));
        $requested = Session::get('validation_toasts') ?? [];

        $toasts = [];

        foreach ($requested as $key) {
            if ($validationErrors->has($key)) {
                $toasts[] = $this->make($validationErrors->first($key), 'error');
            }
        }

        return $toasts;
    }

    public function all(): array
    {
        return array_merge($this->getToasts(), $this->getValidationToasts());
    }
}
