<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],

    'allowed_origins' => ['http://192.168.0.82:8000'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Authorization'],

    'exposed_headers' => ['Authorization'],

    'max_age' => 86400, // Cache preflight response for 1 day

    'supports_credentials' => true, // Allow cookies or Authorization header
];
