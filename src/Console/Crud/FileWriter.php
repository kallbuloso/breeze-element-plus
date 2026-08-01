<?php

declare(strict_types=1);

namespace Kallbuloso\BreezeElementPlus\Console\Crud;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class FileWriter
{
    /** @var array<int, array{label: string, path: string, status: string}> */
    public array $report = [];

    public function __construct(
        private readonly Filesystem $files,
        private readonly Command $command,
        private readonly bool $force,
        private readonly bool $dryRun,
    ) {}

    public function write(string $label, string $path, string $contents): bool
    {
        if ($this->files->exists($path) && $this->files->get($path) === $contents) {
            $this->record($label, $path, 'unchanged');

            return true;
        }

        if ($this->files->exists($path) && ! $this->force && ! $this->dryRun) {
            if (! $this->command->confirm("{$label} already exists at {$path}. Overwrite?", false)) {
                $this->record($label, $path, 'skipped');

                return false;
            }
        }

        if ($this->dryRun) {
            $this->record($label, $path, $this->files->exists($path) ? 'would-overwrite' : 'would-create');

            return true;
        }

        $this->files->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $contents);
        $this->record($label, $path, 'written');

        return true;
    }

    /** @param callable(string): string $transform */
    public function update(string $label, string $path, callable $transform): bool
    {
        if (! $this->files->exists($path)) {
            $this->record($label, $path, 'missing');

            return false;
        }

        $original = $this->files->get($path);
        $updated = $transform($original);

        if ($updated === $original) {
            $this->record($label, $path, 'unchanged');

            return true;
        }

        if ($this->dryRun) {
            $this->record($label, $path, 'would-update');

            return true;
        }

        $this->files->put($path, $updated);
        $this->record($label, $path, 'updated');

        return true;
    }

    private function record(string $label, string $path, string $status): void
    {
        $this->report[] = compact('label', 'path', 'status');
    }
}
