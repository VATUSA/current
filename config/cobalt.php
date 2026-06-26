<?php

return [
    'url' => env('COBALT_URL', ''),
    'token' => env('COBALT_TOKEN', ''),
    'jwt_key' => env('JWT_KEY', ''),
    'cookie_name' => env('COBALT_COOKIE', 'vatusa-cobalt-token'),
    'cookie_domain' => env('COBALT_COOKIE_DOMAIN', ''),
    'login_url' => env('COBALT_LOGIN_URL', ''),
    'use_cobalt_login' => env('USE_COBALT_LOGIN', false),
];