<?php

declare(strict_types=1);

namespace Kallbuloso\BreezeElementPlus\Console\Crud;

use RuntimeException;

class StubProcessor
{
    public function __construct(private readonly string $stubsPath) {}

    /** @param array<string, string> $replacements */
    public function render(string $name, array $replacements): string
    {
        $path = rtrim($this->stubsPath, '/\\').DIRECTORY_SEPARATOR.ltrim($name, '/\\').'.stub';

        if (! is_file($path)) {
            throw new RuntimeException("CRUD stub not found: {$name}");
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException("Unable to read CRUD stub: {$name}");
        }

        return str_replace(array_keys($replacements), array_values($replacements), $contents);
    }
}
