<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    |
    | This value is the configuration for attachment routes. Consider different
    | value for prefix & name-prefix for web and api routes to avoid any route
    | name conflict.
    */
    'routes'              => [
        'web' => [
            'active'      => true,
            'domain'      => '',
            'prefix'      => '',
            'name-prefix' => '',
            'middleware'  => [
                'web',
                'auth'
            ]
        ],
        'api' => [
            'active'      => true,
            'domain'      => '',
            'prefix'      => 'api',
            'name-prefix' => 'api.',
            'middleware'  => [
                'api',
                'auth:api'
            ]
        ]
    ]

];
