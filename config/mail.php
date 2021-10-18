<?php

return [
    'default' => 'smtp',
    'mailers' => [
        'smtp' => [
            'transport'  => 'smtp',
            'host'       => env('MAIL_HOST', 'mail.vatusa.net'),
            'port'       => env('MAIL_PORT', 587),
            'from'       => ['address' => 'no-reply@vatusa.net', 'name' => 'VATUSA Web Services'],
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username'   => env('SUPPORT_EMAIL_USERNAME', 'no-reply@vatusa.net'),
            'password'   => env("SUPPORT_EMAIL_PASSWORD"),
            'timeout'    => null,
            'auth_mode'  => null
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path'      => '/usr/sbin/sendmail -bs',
        ]
    ]
];
