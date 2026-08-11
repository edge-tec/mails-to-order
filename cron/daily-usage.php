<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Services\POPTrackingService;

if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->safeLoad();
}

echo "[" . date('Y-m-d H:i:s') . "] Running daily POP usage reset job...\n";
POPTrackingService::resetDailyUsage();
echo "[" . date('Y-m-d H:i:s') . "] Daily POP usage reset complete.\n";
