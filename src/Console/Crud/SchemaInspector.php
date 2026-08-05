<?php

declare(strict_types=1);

namespace Kallbuloso\BreezeElementPlus\Console\Crud;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SchemaInspector
{
    public function tableExists(string $table): bool
    {
        return Schema::hasTable($table);
    }

    /** @return array<int, array<string, mixed>> */
    public function columns(string $table): array
    {
        return array_map(function (array $column): array {
            $type = (string) ($column['type'] ?? '');

            return [
                'name' => (string) $column['name'],
                'type' => $type,
                'type_name' => (string) ($column['type_name'] ?? Str::before(Str::lower($type), '(')),
                'nullable' => (bool) ($column['nullable'] ?? false),
                'length' => $this->extractLength($type),
            ];
        }, Schema::getColumns($table));
    }

    /** @return array<int, array<string, mixed>> */
    public function foreignKeys(string $table): array
    {
        return Schema::getForeignKeys($table);
    }

    /** @return array<string, mixed>|null */
    public function foreignKeyFor(string $table, string $column): ?array
    {
        foreach ($this->foreignKeys($table) as $foreignKey) {
            if (in_array($column, $foreignKey['columns'] ?? [], true)) {
                return $foreignKey;
            }
        }

        return null;
    }

    public function hasTenantForeignKey(string $table): bool
    {
        $foreignKey = $this->foreignKeyFor($table, 'tenant_id');

        return $foreignKey !== null
            && ($foreignKey['foreign_table'] ?? null) === 'tenants'
            && in_array('id', $foreignKey['foreign_columns'] ?? [], true);
    }

    /** @param array<string, mixed> $column @param array<string, mixed>|null $foreignKey */
    public function classify(array $column, ?array $foreignKey = null): string
    {
        if ($foreignKey !== null) {
            return 'foreign';
        }

        $type = Str::lower((string) ($column['type_name'] ?? $column['type'] ?? ''));

        if ($this->enumValues((string) ($column['type'] ?? '')) !== null) {
            return 'enum';
        }

        return match (true) {
            Str::contains($type, ['boolean', 'bool', 'tinyint(1)']) => 'boolean',
            Str::contains($type, ['int', 'serial']) => 'integer',
            Str::contains($type, ['decimal', 'numeric', 'float', 'double', 'real']) => 'decimal',
            Str::contains($type, ['datetime', 'timestamp']) => 'datetime',
            Str::contains($type, 'date') => 'date',
            Str::contains($type, 'time') => 'time',
            Str::contains($type, ['json', 'jsonb']) => 'json',
            Str::contains($type, ['text', 'clob']) => 'textarea',
            default => 'string',
        };
    }

    /** @return array<int, string>|null */
    public function enumValues(string $type): ?array
    {
        if (! preg_match('/^enum\((.+)\)$/i', trim($type), $matches)) {
            return null;
        }

        return array_values(array_filter(array_map(
            static fn (string $value): string => trim($value, " '\""),
            explode(',', $matches[1]),
        ), static fn (string $value): bool => $value !== ''));
    }

    private function extractLength(string $type): ?int
    {
        return preg_match('/\((\d+)\)/', $type, $matches) === 1 ? (int) $matches[1] : null;
    }
}
