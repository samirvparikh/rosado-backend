<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | The storefront (Vite dev server / built SPA) calls this API from a
    | different origin, so every /api/* route needs CORS headers.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_values(array_unique(array_filter(array_map('trim', explode(',', implode(',', [
        env('CORS_ALLOWED_ORIGINS', ''),
        env('FRONTEND_URL', 'https://rosado.websture.local'),
        'https://rosado.websture.in',
        'http://localhost:5173',
        'http://127.0.0.1:5173',
    ])))))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
