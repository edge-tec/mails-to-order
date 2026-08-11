<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Services\Database;
use App\Services\EmailService;
use App\Models\User;

if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->safeLoad();
}

echo "[" . date('Y-m-d H:i:s') . "] Starting server expiration check cron job...\n";

try {
    // Find active assignments that have expired
    $expiredAssignments = Database::fetchAll(
        "SELECT sa.*, s.host_ip, u.email, u.name as user_name
         FROM server_assignments sa
         JOIN servers s ON sa.server_id = s.id
         JOIN users u ON sa.user_id = u.id
         WHERE sa.status = 'Active' AND sa.expiration_date <= NOW()"
    );

    $count = 0;
    foreach ($expiredAssignments as $exp) {
        // Update assignment status to Expired
        Database::execute(
            "UPDATE server_assignments SET status = 'Expired', updated_at = NOW() WHERE id = ?",
            [$exp['id']]
        );

        // Update server inventory status to Expired
        Database::execute(
            "UPDATE servers SET status = 'Expired', updated_at = NOW() WHERE id = ?",
            [$exp['server_id']]
        );

        // Update order status to Expired
        Database::execute(
            "UPDATE orders SET status = 'Expired', updated_at = NOW() WHERE id = ?",
            [$exp['order_id']]
        );

        // Send email alert to user
        $subject = "Server Subscription Expired — Host IP: {$exp['host_ip']}";
        $html = "<p>Hello {$exp['user_name']},</p><p>Your server subscription for <strong>{$exp['host_ip']}</strong> has expired as of " . date('M d, Y', strtotime($exp['expiration_date'])) . ".</p><p>Please renew your package from your dashboard to restore active status.</p>";
        EmailService::send($exp['email'], $exp['user_name'], $subject, $html, 'server_expired');

        $count++;
        echo " - Updated & notified expired server: {$exp['host_ip']} (User: {$exp['email']})\n";
    }

    echo "[" . date('Y-m-d H:i:s') . "] Expiration check complete. Processed {$count} expired servers.\n";
} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] ERROR: " . $e->getMessage() . "\n";
}
