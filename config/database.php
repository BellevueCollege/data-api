<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'da'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [
        'default' => [
            'driver'   => env('DEFAULT_DB_DRIVER', 'sqlsrv'),
            'host'     => env('DEFAULT_DB_HOST', ''),
            'port'     => env('DEFAULT_DB_PORT', null),
            'database' => env('DEFAULT_DB_DATABASE', ''),
            'username' => env('DEFAULT_DB_USERNAME', ''),
            'password' => env('DEFAULT_DB_PASSWORD', ''),
            'charset'   => env('DEFAULT_DB_CHARSET', 'utf8'),
            'collation' => env('DEFAULT_DB_COLLATION', 'SQL_Latin1_General_CP1_CI_AS'),
        ],

        'da' => [
            'driver'    => env('DA_DB_DRIVER', env('DEFAULT_DB_DRIVER', 'sqlsrv')),
            'host'      => env('DA_DB_HOST', env('DEFAULT_DB_HOST', '')),
            'port'      => env('DA_DB_PORT', env('DEFAULT_DB_PORT', null)),
            'database'  => env('DA_DB_DATABASE', ''),
            'username'  => env('DA_DB_USERNAME', env('DEFAULT_DB_USERNAME', '')),
            'password'  => env('DA_DB_PASSWORD', env('DEFAULT_DB_PASSWORD', '')),
            'prefix'    => '',
            'charset'   => env('DEFAULT_DB_CHARSET', 'utf8'),
            'collation' => env('DEFAULT_DB_COLLATION', 'SQL_Latin1_General_CP1_CI_AS'),
        ],
        'ods' => [
            'driver'    => env('ODS_DB_DRIVER', env('DEFAULT_DB_DRIVER', 'sqlsrv')),
            'host'      => env('ODS_DB_HOST', env('DEFAULT_DB_HOST', '')),
            'port'      => env('ODS_DB_PORT', env('DEFAULT_DB_PORT', null)),
            'database'  => env('ODS_DB_DATABASE', ''),
            'username'  => env('ODS_DB_USERNAME', env('DEFAULT_DB_USERNAME', '')),
            'password'  => env('ODS_DB_PASSWORD', env('DEFAULT_DB_PASSWORD', '')),
            'prefix'    => '',
            'charset'   => env('DEFAULT_DB_CHARSET', 'utf8'),
            'collation' => env('DEFAULT_DB_COLLATION', 'SQL_Latin1_General_CP1_CI_AS'),
        ],
        'pciforms' => [
            'driver'    => env('PCIFORMS_DB_DRIVER', env('DEFAULT_DB_DRIVER', 'sqlsrv')),
            'host'      => env('PCIFORMS_DB_HOST', env('DEFAULT_DB_HOST', '')),
            'port'      => env('PCIFORMS_DB_PORT', env('DEFAULT_DB_PORT', null)),
            'database'  => env('PCIFORMS_DB_DATABASE', ''),
            'username'  => env('PCIFORMS_DB_USERNAME', env('DEFAULT_DB_USERNAME', '')),
            'password'  => env('PCIFORMS_DB_PASSWORD', env('DEFAULT_DB_PASSWORD', '')),
            'prefix'    => '',
            'charset'   => env('DEFAULT_DB_CHARSET', 'utf8'),
            'collation' => env('DEFAULT_DB_COLLATION', 'SQL_Latin1_General_CP1_CI_AS'),
        ],
        'evalforms' => [
            'driver'    => env('EVALFORMS_DB_DRIVER', env('DEFAULT_DB_DRIVER', 'sqlsrv')),
            'host'      => env('EVALFORMS_DB_HOST', env('DEFAULT_DB_HOST', '')),
            'port'      => env('EVALFORMS_DB_PORT', env('DEFAULT_DB_PORT', null)),
            'database'  => env('EVALFORMS_DB_DATABASE', ''),
            'username'  => env('EVALFORMS_DB_USERNAME', env('DEFAULT_DB_USERNAME', '')),
            'password'  => env('EVALFORMS_DB_PASSWORD', env('DEFAULT_DB_PASSWORD', '')),
            'prefix'    => '',
            'charset'   => env('DEFAULT_DB_CHARSET', 'utf8'),
            'collation' => env('DEFAULT_DB_COLLATION', 'SQL_Latin1_General_CP1_CI_AS'),
        ],
        'empdirectory' => [
            'driver'    => env('EMPDIR_DB_DRIVER', env('DEFAULT_DB_DRIVER', 'sqlsrv')),
            'host'      => env('EMPDIR_DB_HOST', env('DEFAULT_DB_HOST', '')),
            'port'      => env('EMPDIR_DB_PORT', env('DEFAULT_DB_PORT', null)),
            'database'  => env('EMPDIR_DB_DATABASE', ''),
            'username'  => env('EMPDIR_DB_USERNAME', env('DEFAULT_DB_USERNAME', '')),
            'password'  => env('EMPDIR_DB_PASSWORD', env('DEFAULT_DB_PASSWORD', '')),
            'prefix'    => '',
            'charset'   => env('DEFAULT_DB_CHARSET', 'utf8'),
            'collation' => env('DEFAULT_DB_COLLATION', 'SQL_Latin1_General_CP1_CI_AS'),
        ],
        'copilot' => [
            'driver'    => env('COPILOT_DB_DRIVER', env('DEFAULT_DB_DRIVER', 'sqlsrv')),
            'host'      => env('COPILOT_DB_HOST', env('DEFAULT_DB_HOST', '')),
            'port'      => env('COPILOT_DB_PORT', env('DEFAULT_DB_PORT', null)),
            'database'  => env('COPILOT_DB_DATABASE', ''),
            'username'  => env('COPILOT_DB_USERNAME', env('DEFAULT_DB_USERNAME', '')),
            'password'  => env('COPILOT_DB_PASSWORD', env('DEFAULT_DB_PASSWORD', '')),
            'prefix'    => '',
            'charset'   => env('DEFAULT_DB_CHARSET', 'utf8'),
            'collation' => env('DEFAULT_DB_COLLATION', 'SQL_Latin1_General_CP1_CI_AS'),
        ]

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => 'predis',

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

    ],

];
