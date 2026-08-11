<?php

return [
    'host' => env('SMTP_HOST', 'smtp.mailtrap.io'),
    'port' => (int) env('SMTP_PORT', 2525),
    'username' => env('SMTP_USERNAME', ''),
    'password' => env('SMTP_PASSWORD', ''),
    'encryption' => env('SMTP_ENCRYPTION', 'tls'),
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Server Provisioning Portal'),
    ],
];
