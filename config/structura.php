<?php

/*
    |--------------------------------------------------------------------------
    | Structura Configuration
    |--------------------------------------------------------------------------
    | This file is for configuring the Structura package settings.
    | You can customize namespaces, paths, and other options here.
    */

return [

    /*
    |--------------------------------------------------------------------------
    | Namespaces
    |--------------------------------------------------------------------------
    | Defines the base namespaces for different types of classes.
    |
    | ⚠️ Note:
    | When changing any namespace value, make sure to also update the
    | corresponding entry in the "paths" section below. Both configurations
    | must stay in sync to ensure proper class resolution and autoloading.
    |
    */
    'namespaces' => [
        'action' => 'App\\Actions',
        'cache' => 'App\\Caches',
        'dto' => 'App\\DTOs',
        'enum' => 'App\\Enums',
        'helper' => 'App\\Helpers',
        'service' => 'App\\Services',
        'trait' => 'App\\Concerns',
    ],

    /*
    |--------------------------------------------------------------------------
    | Paths
    |--------------------------------------------------------------------------
    | Defines the base directory paths for different types of classes.
    |
    | ⚠️ Note:
    | These paths must match the namespaces defined above. If a namespace
    | is modified, its related path should be updated accordingly to avoid
    | autoloading issues.
    |
    */
    'paths' => [
        'action' => app_path('Actions'),
        'cache' => app_path('Caches'),
        'dto' => app_path('DTOs'),
        'enum' => app_path('Enums'),
        'helper' => app_path('Helpers'),
        'service' => app_path('Services'),
        'trait' => app_path('Concerns'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Options
    |--------------------------------------------------------------------------
    | Defines the default behavior and flags applied when generating or
    | registering each type of class.
    |
    | These options act as sensible defaults and can usually be overridden
    | at runtime or per-command when needed.
    |   
    */
    'default_optins' => [
        'action' => [
            'construct' => false,
            'execute' => true,
            'handle' => false,
            'invokable' => false,
            'raw' => false,
        ],
        'cache' => [
            'extend' => false,
            'raw' => false,
        ],
        'dto' => [
            'no-final' => false,
            'no-readonly' => false,
            'no-construct' => false,
            'trait' => false,
            'raw' => false,
        ],
        'enum' => [
            'label' => false,
            'trait' => false,
            'backed' => 'string',  // 'string'|'int'|null
        ],
        'helper' => [
            'example' => true,
            'global' => false,
            'stub' => false,
            'raw' => false,
        ],
        'service' => [
            'construct' => true,
            'raw' => false,
        ],
    ],
];
