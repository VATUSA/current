<?php

return [
	'driver' => 'smtp',
	'host' => env('MAIL_HOST', 'mail.vatusa.ne'),
	'port' => env('MAIL_PORT', 587),
	'from' => ['address' =>  'no-reply@vatusa.net', 'name' => 'VATUSA'],
	'encryption' => env('MAIL_ENCRYPTION', 'tls'),
	'username' => env('SUPPORT_EMAIL_USERNAME', 'no-reply@vatusa.net'),
	'password' => env("SUPPORT_EMAIL_PASSWORD"),

	'sendmail' => '/usr/sbin/sendmail -bs',
	'pretend' => false,
];
