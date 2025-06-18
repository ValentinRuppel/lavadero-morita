<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => 'web', // El guardia por defecto para clientes
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here. Here are the default sessions and token based guards.
    |
    | Laravel implements a session based authentication guard that uses
    | sessions and cookies to maintain its state. The most common guard
    | is a "web" guard, which uses the session driver.
    |
    */

    'guards' => [
        'web' => [ // Guardia para clientes (tabla 'users')
            'driver' => 'session',
            'provider' => 'users',
        ],
        'admin' => [ // <-- ¡NUEVO GUARDIA PARA ADMINISTRADORES!
            'driver' => 'session',
            'provider' => 'administrators', // Hace referencia al nuevo proveedor
        ],
        'sanctum' => [
            'driver' => 'sanctum',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication providers for your application are defined below.
    | You may add as many providers as you want.
    |
    | Laravel has several default driver implementations while you may also
    | develop your own. Typically, an Eloquent user provider uses the
    | Eloquent ORM to retrieve users based on their unique "id".
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [ // Proveedor para clientes (tabla 'users')
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
        'administrators' => [ // <-- ¡NUEVO PROVEEDOR PARA ADMINISTRADORES!
            'driver' => 'eloquent',
            'model' => App\Models\Administrator::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | You may specify how many seconds before a reset token expires. This
    | security feature keeps the tokens short-lived so they have less
    | time to be guessed. You may change this security feature as needed.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
        'administrators' => [ 
            'provider' => 'administrators',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the amount of seconds before a password confirmation
    | times out and the user is prompted to re-enter their password via the
    | authentication screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => 10800,

];