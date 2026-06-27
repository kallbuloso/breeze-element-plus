<?php

namespace App\Providers;

use App\Hashing\PepperedHasher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;

class HashServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $previousPeppers = $this->previousPeppers();

        Hash::extend('argon2id_pepper', function () use ($previousPeppers) {
            return new PepperedHasher(
                inner: Hash::driver('argon2id'),
                pepperId: (string) config('hashing.pepper.id'),
                pepper: config('hashing.pepper.current'),
                previousPeppers: $previousPeppers,
                legacyHashers: [
                    'bcrypt' => Hash::driver('bcrypt'),
                    'argon2i' => Hash::driver('argon'),
                    'argon2id' => Hash::driver('argon2id'),
                ],
                allowLegacy: (bool) config('hashing.pepper.allow_legacy'),
            );
        });
    }

    /**
     * @return array<string, string>
     */
    private function previousPeppers(): array
    {
        $peppers = [];

        foreach (explode(',', (string) config('hashing.pepper.previous')) as $entry) {
            if (! str_contains($entry, ':')) {
                continue;
            }

            [$id, $pepper] = array_map('trim', explode(':', $entry, 2));

            if ($id !== '' && $pepper !== '') {
                $peppers[$id] = $pepper;
            }
        }

        return $peppers;
    }
}
