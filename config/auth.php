<?php

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Superadmin;
use App\Models\User;

return [

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'siswa' => [
            'driver' => 'session',
            'provider' => 'siswa',
        ],

        'guru' => [
            'driver' => 'session',
            'provider' => 'guru',
        ],

        'superadmin' => [
            'driver' => 'session',
            'provider' => 'superadmin',
        ],

        'sanctum' => [
            'driver' => 'sanctum',
            'provider' => null,
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', User::class),
        ],

        'siswa' => [
            'driver' => 'eloquent',
            'model' => Siswa::class,
        ],

        'guru' => [
            'driver' => 'eloquent',
            'model' => Guru::class,
        ],

        'superadmin' => [
            'driver' => 'eloquent',
            'model' => Superadmin::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],

        'siswa' => [
            'provider' => 'siswa',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],

        'guru' => [
            'provider' => 'guru',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],

        'superadmin' => [
            'provider' => 'superadmin',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
