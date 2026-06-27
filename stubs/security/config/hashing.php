<?php

return [
    'driver' => env('HASH_DRIVER', 'bcrypt'),

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12),
        'verify' => env('HASH_VERIFY', true),
        'limit' => env('BCRYPT_LIMIT', null),
    ],

    'argon' => [
        'memory' => env('ARGON_MEMORY', 32768),
        'threads' => env('ARGON_THREADS', 1),
        'time' => env('ARGON_TIME', 3),
        'verify' => env('HASH_VERIFY', true),
    ],

    'pepper' => [
        'id' => env('HASH_PEPPER_ID', 'v1'),
        'current' => env('HASH_PEPPER'),
        'previous' => env('HASH_PREVIOUS_PEPPERS', ''),
        'allow_legacy' => env('HASH_ALLOW_LEGACY', true),
    ],

    'rehash_on_login' => true,
];
