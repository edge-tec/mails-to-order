<?php

namespace App\Models;

use App\Services\Database;

class AdminLog {
    public static function log(string $action, ?string $targetType = null, ?int $targetId = null, string $details = ''): bool {
        $user = auth_user();
        if (!$user) return false;

        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'CLI/Unknown';

        try {
            Database::insert(
                "INSERT INTO admin_logs (admin_id, action, target_type, target_id, details, ip_address, user_agent, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())",
                [$user['id'], $action, $targetType, $targetId, $details, $ip, $agent]
            );
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getAll(): array {
        return Database::fetchAll(
            "SELECT al.*, u.name as admin_name, u.email as admin_email
             FROM admin_logs al
             JOIN users u ON al.admin_id = u.id
             ORDER BY al.id DESC
             LIMIT 500"
        );
    }
}
