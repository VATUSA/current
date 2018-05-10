<?php

return [
	'driver' => 'smtp',
	'host' => 'mail.vatusa.net',
	'port' => 587,
	'from' => ['address' => 'no-reply@vatusa.net', 'name' => 'VATUSA'],
	'encryption' => env('MAIL_ENCRYPTION', 'tls'),
	'username' => 'no-reply@vatusa.net',
	'password' => env("SUPPORT_EMAIL_PASSWORD"),

	'sendmail' => '/usr/sbin/sendmail -bs',
	'pretend' => false,
];
