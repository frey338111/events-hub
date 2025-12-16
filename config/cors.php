<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'customer/*',
        'login',
        'logout',
    ],

    // Allow GET, POST, PUT, PATCH, DELETE, OPTIONS
    'allowed_methods' => ['*'],

    // React development origins:
    'allowed_origins' => [
        'http://localhost:5173',
        'http://localhost:3000',
    ],

    // Allow all headers
    'allowed_headers' => ['*'],

    // Expose no special headers
    'exposed_headers' => [],

    'max_age' => 0,

    /*
     * IMPORTANT:
     * Must be TRUE for session-based authentication in React.
     * Enables cookies to be sent cross-site.
     */
    'supports_credentials' => true,
];
