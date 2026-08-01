<?php

declare(strict_types=1);

namespace Kallbuloso\BreezeElementPlus\Console\Crud;

use Illuminate\Support\Str;
use RuntimeException;

class IconSuggester
{
    /** @var array<string, string> */
    private const ICONS = [
        'user' => 'ri:user-line',
        'client' => 'ri:user-star-line',
        'product' => 'ri:box-3-line',
        'category' => 'ri:price-tag-3-line',
        'order' => 'ri:shopping-bag-3-line',
        'invoice' => 'ri:file-text-line',
        'setting' => 'ri:settings-3-line',
    ];

    public function suggest(string $name): string
    {
        $needle = Str::lower($name);

        foreach (self::ICONS as $keyword => $icon) {
            if (Str::contains($needle, $keyword)) {
                return $icon;
            }
        }

        return 'ri:apps-line';
    }
}
