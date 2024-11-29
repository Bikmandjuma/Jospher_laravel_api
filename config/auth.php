<?php

return [

    'defaults' => [
        'guard' => 'api',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'jwt',
            'provider' => 'users',
            'hash' => false,  // This is fine if you're not using password hashing for JWT authentication
        ],

        'admin' => [
            'driver' => 'jwt',
            'provider' => 'admins',
            'hash' => false, // Optional, if you don't require hashing for the admin JWT
        ],

        'user' => [
            'driver' => 'jwt',
            'provider' => 'userss',
            'hash' => false, // Optional, if you don't require hashing for the admin JWT
        ],

    ],

    'providers' => [

        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class, // Make sure this points to your Admin model
        ],

        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,  // Update with the correct user model if necessary
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60, // Time in minutes before a password reset link expires
            'throttle' => 60, // Limit the number of reset attempts in a minute
        ],
    ],

    'password_timeout' => 10800, // Timeout in seconds (3 hours) for password reset attempts
];
