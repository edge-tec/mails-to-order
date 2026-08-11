<?php

namespace App\Services;

use PDO;
use Exception;

class InstallerService {

    public static function checkRequirements(): array {
        return [
            'php_version' => [
                'name' => 'PHP 8.2 or Higher',
                'status' => version_compare(PHP_VERSION, '8.2.0', '>='),
                'current' => PHP_VERSION
            ],
            'pdo' => [
                'name' => 'PDO MySQL Extension',
                'status' => extension_loaded('pdo') && extension_loaded('pdo_mysql'),
                'current' => extension_loaded('pdo_mysql') ? 'Enabled' : 'Disabled'
            ],
            'openssl' => [
                'name' => 'OpenSSL Extension (AES-256 Encryption)',
                'status' => extension_loaded('openssl'),
                'current' => extension_loaded('openssl') ? 'Enabled' : 'Disabled'
            ],
            'json' => [
                'name' => 'JSON Extension',
                'status' => extension_loaded('json'),
                'current' => extension_loaded('json') ? 'Enabled' : 'Disabled'
            ],
            'curl' => [
                'name' => 'cURL Extension',
                'status' => extension_loaded('curl'),
                'current' => extension_loaded('curl') ? 'Enabled' : 'Disabled'
            ],
            'mbstring' => [
                'name' => 'Mbstring Extension',
                'status' => extension_loaded('mbstring'),
                'current' => extension_loaded('mbstring') ? 'Enabled' : 'Disabled'
            ],
            'storage_writable' => [
                'name' => 'storage/ Writable Directory',
                'status' => is_writable(__DIR__ . '/../../storage'),
                'current' => is_writable(__DIR__ . '/../../storage') ? 'Writable' : 'Not Writable'
            ],
            'vendor_autoloader' => [
                'name' => 'Composer Vendor Autoloader',
                'status' => file_exists(__DIR__ . '/../../vendor/autoload.php'),
                'current' => file_exists(__DIR__ . '/../../vendor/autoload.php') ? 'Installed' : 'Missing'
            ]
        ];
    }

    public static function testDatabase(array $config): bool {
        $dsn = "mysql:host={$config['host']};port={$config['port']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['username'], $config['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        return true;
    }

    public static function runMigrationsAndSeeders(array $dbConfig): void {
        $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        // Run migration SQL file
        $sqlFile = __DIR__ . '/../../database/migrations/001_create_tables.sql';
        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            $pdo->exec($sql);
        }
    }

    public static function generateEnvFile(array $params): void {
        $appKey = 'key_' . bin2hex(random_bytes(16));

        $envContent = <<<ENV
APP_NAME="{$params['site_name']}"
APP_ENV=production
APP_URL="{$params['site_url']}"
APP_KEY="{$appKey}"

DB_HOST="{$params['db_host']}"
DB_PORT="{$params['db_port']}"
DB_DATABASE="{$params['db_name']}"
DB_USERNAME="{$params['db_user']}"
DB_PASSWORD="{$params['db_pass']}"

SMTP_HOST="{$params['smtp_host']}"
SMTP_PORT="{$params['smtp_port']}"
SMTP_USERNAME="{$params['smtp_user']}"
SMTP_PASSWORD="{$params['smtp_pass']}"
SMTP_ENCRYPTION="{$params['smtp_enc']}"
MAIL_FROM_ADDRESS="{$params['mail_from_addr']}"
MAIL_FROM_NAME="{$params['mail_from_name']}"

STORAGE_PATH=storage
UPLOAD_PATH=storage/uploads
PUBLIC_UPLOAD_PATH=public/uploads
ENV;

        file_put_contents(__DIR__ . '/../../.env', $envContent);
    }
}
