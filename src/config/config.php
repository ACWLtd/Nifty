<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Routes
    |--------------------------------------------------------------------------
    |
    */

    'routes' => [
        'login' => [
            'route' => 'login'
        ],

        'logout' => [
            'route' => 'logout'
        ],

        'admin'     => [
            'route' => 'admin'
        ],

        'pages'     => [
            'route' => 'admin/pages'
        ],

        'posts'     => [
            'route' => 'admin/posts'
        ],

        'locales'     => [
            'route' => 'admin/locales'
        ],

        'events'     => [
            'route' => 'admin/events'
        ],

        'users'     => [
            'route' => 'admin/users'
        ],
    ],

];
