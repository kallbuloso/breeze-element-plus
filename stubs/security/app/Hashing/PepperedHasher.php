<?php

namespace App\Hashing;

use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use RuntimeException;
use SensitiveParameter;

class PepperedHasher implements HasherContract
{
    private const PREFIX = '$peppered$';

    /**
     * @param  array<string, string>  $previousPeppers
     * @param  array<string, HasherContract>  $legacyHashers
     */
    public function __construct(
        protected HasherContract $inner,
        protected string $pepperId,
        protected ?string $pepper,
        protected array $previousPeppers = [],
        protected array $legacyHashers = [],
        protected bool $allowLegacy = true,
    ) {
        if (blank($this->pepperId) || blank($this->pepper)) {
            throw new RuntimeException('HASH_PEPPER_ID e HASH_PEPPER devem estar configurados.');
        }

        if (str_contains($this->pepperId, '$')) {
            throw new RuntimeException('HASH_PEPPER_ID não pode conter o caractere $.');
        }
    }

    public function info($hashedValue)
    {
        $parsed = $this->parse($hashedValue);

        return $this->inner->info($parsed['hash'] ?? $hashedValue);
    }

    public function make(#[SensitiveParameter] $value, array $options = [])
    {
        $hash = $this->inner->make($this->pepper($value, $this->pepper), $options);

        return self::PREFIX.$this->pepperId.'$'.$hash;
    }

    public function check(#[SensitiveParameter] $value, $hashedValue, array $options = [])
    {
        if ($hashedValue === '' || $hashedValue === null) {
            return false;
        }

        if ($parsed = $this->parse($hashedValue)) {
            $pepper = $parsed['id'] === $this->pepperId
                ? $this->pepper
                : ($this->previousPeppers[$parsed['id']] ?? null);

            return filled($pepper)
                && $this->inner->check($this->pepper($value, $pepper), $parsed['hash'], $options);
        }

        if (! $this->allowLegacy) {
            return false;
        }

        $algorithm = password_get_info($hashedValue)['algoName'] ?? 'unknown';
        $legacyHasher = $this->legacyHashers[$algorithm] ?? null;

        return $legacyHasher?->check($value, $hashedValue, $options) ?? false;
    }

    public function needsRehash($hashedValue, array $options = [])
    {
        $parsed = $this->parse($hashedValue);

        if (! $parsed || $parsed['id'] !== $this->pepperId) {
            return true;
        }

        return $this->inner->needsRehash($parsed['hash'], $options);
    }

    public function verifyConfiguration($hashedValue): bool
    {
        $parsed = $this->parse($hashedValue);

        if (! $parsed || $parsed['id'] !== $this->pepperId) {
            return false;
        }

        return ! method_exists($this->inner, 'verifyConfiguration')
            || $this->inner->verifyConfiguration($parsed['hash']);
    }

    /**
     * @return array{id: string, hash: string}|null
     */
    protected function parse(mixed $hashedValue): ?array
    {
        if (! is_string($hashedValue) || ! str_starts_with($hashedValue, self::PREFIX)) {
            return null;
        }

        $remainder = substr($hashedValue, strlen(self::PREFIX));
        $separator = strpos($remainder, '$');

        if ($separator === false) {
            return null;
        }

        return [
            'id' => substr($remainder, 0, $separator),
            'hash' => substr($remainder, $separator + 1),
        ];
    }

    protected function pepper(#[SensitiveParameter] mixed $value, string $pepper): string
    {
        return hash_hmac('sha256', (string) $value, $pepper);
    }
}
