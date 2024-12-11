<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS Options
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for Cross-Origin Resource Sharing
    | (CORS). By default, this package will allow all origins, but you may
    | customize it to your needs.
    |
    */

    'supports_credentials' => true,
    'allowed_origins' => ['*'],  // Add your frontend origin here
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Authorization'],
    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
    'exposed_headers' => [],
    'max_age' => 0,

];
