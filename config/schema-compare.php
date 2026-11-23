<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Allowed SQL Server hosts
    |--------------------------------------------------------------------------
    |
    | A non-empty list is required. schema:compare refuses to run unless every
    | connection host used for warehouse reads is listed here.
    |
    */

    'allowed_hosts' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('SCHEMA_COMPARE_ALLOWED_HOSTS', ''))
    ))),

    /*
    |--------------------------------------------------------------------------
    | Connections and objects to check
    |--------------------------------------------------------------------------
    |
    | Warehouse objects come from SqliteWarehouseSchema. da tables are name-only
    | (Laravel migrations have no MSSQL type annotations).
    | pciforms and evalforms are excluded until SQLite stand-ins exist.
    |
    */

    'compare_connections' => ['da', 'ods', 'empdirectory', 'copilot'],

    'sqlite_connections' => [
        'default',
        'da',
        'ods',
        'pciforms',
        'evalforms',
        'empdirectory',
        'copilot',
    ],

    'da_objects' => [
        'Clients',
        'BlockReasons',
        'users',
        'sessions',
        'cache',
        'cache_locks',
    ],

];
