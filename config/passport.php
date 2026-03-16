<?php

return [

    'ttl' => [
        'resolved' => env('PASSPORT_RESOLVED_TTL', 2 * 60 * 60),
        'fallback' => env('PASSPORT_FALLBACK_TTL', 24 * 60 * 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Storage Configuration
    |--------------------------------------------------------------------------
    |
    | This section allows you to define the database connection you want to use.
    | By customizing these settings, you can isolate the storage from your
    | application's main tables as needed.
    |
    */

    'database' => [

        /*
        |--------------------------------------------------------------------------
        | Database Connection
        |--------------------------------------------------------------------------
        |
        | By default, it falls back to the default database connection.
        |
        */

        'connection' => env('PASSPORT_CONNECTION', env('DB_CONNECTION', 'mysql')),

        /*
        |--------------------------------------------------------------------------
        | Token Storage Table
        |--------------------------------------------------------------------------
        |
        | If you wish to store it in a custom table, you can change the table name.
        |
        */

        'table' => env('PASSPORT_TABLE', 'passport'),
    ],
];
