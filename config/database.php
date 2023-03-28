<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PDO Fetch Style
    |--------------------------------------------------------------------------
    |
    | By default, database results will be returned as instances of the PHP
    | stdClass object; however, you may desire to retrieve records in an
    | array format for simplicity. Here you can tweak the fetch style.
    |
    */

    'fetch' => PDO::FETCH_CLASS,

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

    'default' => 'mysql',

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
        'mysql'  => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', 'localhost'),
            //'host'      => [env('DB_HOST2', 'localhost'), env('DB_HOST1','')],
            'database'  => env('DB_DATABASE', 'forge'),
            'username'  => env('DB_USERNAME', 'forge'),
            'password'  => env('DB_PASSWORD', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'email'  => [
            'driver'    => env('DB_EMAIL_CONNECTION', 'mysql'),
            'host'      => env('DB_EMAIL_HOST', '127.0.0.1'),
            'port'      => env('DB_EMAIL_PORT', 3306),
            'database'  => env('DB_EMAIL_DATABASE', 'email'),
            'username'  => env('DB_EMAIL_USERNAME', ''),
            'password'  => env('DB_EMAIL_PASSWORD', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false
        ],
        'forum'  => [
            'driver'    => env('DB_FORUM_CONNECTION', 'mysql'),
            'host'      => env('DB_FORUM_HOST', '127.0.0.1'),
            'port'      => env('DB_FORUM_PORT', 3306),
            'database'  => env('DB_FORUM_DATABASE', 'forum'),
            'username'  => env('DB_FORUM_USERNAME', 'forum'),
            'password'  => env('DB_FORUM_PASSWORD', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false
        ],
        'moodle' => [
            'driver'   => env('DB_MOODLE_CONNECTION', 'mysql'),
            'host'     => env('DB_MOODLE_HOST', '127.0.0.1'),
            'port'     => env('DB_MOODLE_PORT', 3306),
            'database' => env('DB_MOODLE_DATABASE', 'forum'),
            'username' => env('DB_MOODLE_USERNAME', 'forum'),
            'password' => env('DB_MOODLE_PASSWORD', ''),
            'prefix'   => 'mdl_',
            'strict'   => false
        ],
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

        'cluster' => false,

        'default' => [
            'scheme' => 'tls',
            'host'     => env("REDIS_HOST", ''),
            'password' => env('REDIS_PASSWORD', null),
            'port'     => env("REDIS_PORT", ''),
            'database' => 0,
        ],

    ],

];
