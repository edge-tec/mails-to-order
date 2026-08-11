<?php

return [
    'name' => env('APP_NAME', 'Server Ordering System'),
    'env' => env('APP_ENV', 'production'),
    'url' => rtrim(env('APP_URL', 'http://localhost:8000'), '/'),
    'key' => env('APP_KEY', 'default_32_bytes_secret_key_12345'),
    'timezone' => 'UTC',
    'storage_path' => __DIR__ . '/../storage',
    'upload_path' => __DIR__ . '/../storage/uploads',
    'public_upload_path' => __DIR__ . '/../public/uploads',
];
