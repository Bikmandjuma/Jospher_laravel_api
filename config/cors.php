<?php

// return [

//     'paths' => ['api/*', 'sanctum/csrf-cookie'],

//     'allowed_methods' => ['*'],

//     'allowed_origins' => ['*'],

//     'allowed_origins_patterns' => [],

//     'allowed_headers' => ['*'],

//     'exposed_headers' => [],

//     'max_age' => 0,

//     'supports_credentials' => false,

// ];

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    // Specifies the paths for which CORS policies should be applied.
    // Here, it applies to all routes starting with `api/` and the Sanctum CSRF cookie endpoint.

    'allowed_methods' => ['*'],
    // Allows all HTTP methods (GET, POST, PUT, DELETE, etc.).
    // Use '*' only if you want to allow unrestricted method access.

    'allowed_origins' => ['*'],
    // Permits requests from any origin.
    // For production, it's safer to replace '*' with a specific domain (e.g., `https://example.com`).

    'allowed_origins_patterns' => [],
    // Allows patterns for origins. Currently unused.

    'allowed_headers' => ['*'],
    // Accepts any headers in the incoming request. This is often necessary for modern APIs.

    'exposed_headers' => [],
    // No headers are exposed to the browser in the response.

    'max_age' => 0,
    // No caching of preflight requests.

    'supports_credentials' => false,
    // Disables support for cookies or authorization headers.
    // Set this to `true` if your API needs to handle authenticated sessions (e.g., Sanctum or JWT).

];
