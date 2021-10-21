<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => '',
        'secret' => '',
    ],

    'mandrill' => [
        'secret' => '',
    ],

    'ses' => [
        'key'    => '',
        'secret' => '',
        'region' => 'us-east-1',
    ],

    'stripe' => [
        'model'  => 'User',
        'secret' => '',
    ],

    'discord' => [
        'client_id'      => env('DISCORD_CLIENT_ID'),
        'client_secret'  => env('DISCORD_CLIENT_SECRET'),
        'redirect'       => env('DISCORD_REDIRECT'),
        'botserver'      => env('DISCORD_BOT_SERVER', 'http://discord-bot:3000'),
        'guildId'        => env('DISCORD_GUILD_ID'),
        'botToken'       => env('DISCORD_BOT_TOKEN'),
        'botPermissions' => env('DISCORD_BOT_PERMISSIONS')
    ],

    'moodle' => [
        'url'       => env('MOODLE_URL', 'https://academy.vatusa.net'),
        'token'     => env('MOODLE_TOKEN'),
        'token_sso' => env('MOODLE_TOKEN_SSO')
    ],

    'vatsim' => [
        'apiToken' => env('VATSIM_API_TOKEN', '')
    ],

    'kb' => [
        'joinQuestionId' => env('KB_JOIN_QUESTION_ID')
    ]
];
