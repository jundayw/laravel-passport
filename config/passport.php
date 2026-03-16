<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Passport API Signature Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file controls the behavior of the lightweight API
    | request verification and response signature package. You may adjust
    | these settings as needed for your application.
    |
    */

    'enabled' => env('PASSPORT_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Ignore Rules
    |--------------------------------------------------------------------------
    |
    | These settings allow you to selectively bypass request verification
    | and/or response signature generation. This can be helpful during
    | development, testing, or when integrating with third-party services
    | that do not support signatures.
    |
    | - 'request': When true, the package will not verify the signature of
    |              incoming API requests. All requests will be accepted as-is.
    | - 'response': When true, the package will not sign outgoing API responses.
    |
    */

    'ignore' => [
        'request'  => env('PASSPORT_IGNORE_VERIFICATION', false),
        'response' => env('PASSPORT_IGNORE_SIGNATURE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Time-to-Live (TTL) Settings
    |--------------------------------------------------------------------------
    |
    | These values define how long (in seconds) certain signature-related data
    | should be cached. The 'resolved' TTL applies to successfully verified
    | requests or resolved signatures, while the 'fallback' TTL is used when
    | a temporary issue occurs, preventing repeated processing within a short
    | time window.
    |
    */

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
