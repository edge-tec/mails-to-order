<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Services\POPTrackingService;

if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->safeLoad();
}

echo "[" . date('Y-m-d H:i:s') . "] Running monthly POP usage reset & statistics consolidation...\n";
POPTrackingService::resetMonthlyUsage();
echo "[" . date('Y-m-d H:i:s') . "] Monthly POP usage reset complete.\n";
