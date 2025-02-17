<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    | You can customise the Middleware that should be loaded.
    | The localizationRedirect middleware is automatically loaded for both
    | Backend and Frontend routes.
    */
    'middleware' => [
        'backend' => [
            'auth.admin',
        ],
        'frontend' => [
        ],
        'api' => [
            'api',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Check if CodeEcho was installed
    |--------------------------------------------------------------------------
    */
    'is_installed' => env('INSTALLED', false),
];
