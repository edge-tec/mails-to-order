<?php

namespace App\Models;

use App\Services\Database;
use App\Services\EncryptionService;

class Server {
    public static function getAll(): array {
        return Database::fetchAll("SELECT * FROM servers ORDER BY id DESC");
    }

    public static function getAvailable(): array {
        return Database::fetchAll("SELECT * FROM servers WHERE status = 'Available' ORDER BY id ASC");
    }

    public static function findById(int $id): ?array {
        return Database::fetch("SELECT * FROM servers WHERE id = ?", [$id]);
    }

    public static function create(array $data): int {
        $encPassword = EncryptionService::encrypt($data['password']);
        $serverId = (int) Database::insert(
            "INSERT INTO servers (host_ip, ssh_port, username, encrypted_password, server_type, location, provider, status, notes, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())",
            [
                $data['host_ip'],
                (int)($data['ssh_port'] ?? 22),
                $data['username'],
                $encPassword,
                $data['server_type'] ?? 'VPS',
                $data['location'] ?? 'USA',
                $data['provider'] ?? 'Internal Cloud',
                $data['status'] ?? 'Available',
                $data['notes'] ?? ''
            ]
        );

        Database::insert(
            "INSERT INTO server_credentials (server_id, host_ip, username, encrypted_password, ssh_port, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())",
            [$serverId, $data['host_ip'], $data['username'], $encPassword, (int)($data['ssh_port'] ?? 22)]
        );

        return $serverId;
    }

    public static function update(int $id, array $data): bool {
        $params = [
            $data['host_ip'],
            (int)($data['ssh_port'] ?? 22),
            $data['username'],
            $data['server_type'] ?? 'VPS',
            $data['location'] ?? 'USA',
            $data['provider'] ?? 'Internal Cloud',
            $data['status'] ?? 'Available',
            $data['notes'] ?? '',
            $id
        ];

        $sql = "UPDATE servers SET host_ip = ?, ssh_port = ?, username = ?, server_type = ?, location = ?, provider = ?, status = ?, notes = ?, updated_at = NOW()";

        if (!empty($data['password'])) {
            $encPassword = EncryptionService::encrypt($data['password']);
            $sql = "UPDATE servers SET host_ip = ?, ssh_port = ?, username = ?, encrypted_password = ?, server_type = ?, location = ?, provider = ?, status = ?, notes = ?, updated_at = NOW() WHERE id = ?";
            $params = [
                $data['host_ip'],
                (int)($data['ssh_port'] ?? 22),
                $data['username'],
                $encPassword,
                $data['server_type'] ?? 'VPS',
                $data['location'] ?? 'USA',
                $data['provider'] ?? 'Internal Cloud',
                $data['status'] ?? 'Available',
                $data['notes'] ?? '',
                $id
            ];

            Database::execute(
                "UPDATE server_credentials SET host_ip = ?, username = ?, encrypted_password = ?, ssh_port = ?, updated_at = NOW() WHERE server_id = ?",
                [$data['host_ip'], $data['username'], $encPassword, (int)($data['ssh_port'] ?? 22), $id]
            );
        } else {
            $sql .= " WHERE id = ?";
        }

        return Database::execute($sql, $params) >= 0;
    }

    public static function assignToOrder(int $serverId, int $userId, int $orderId, int $months = 1): int {
        $expDate = date('Y-m-d H:i:s', strtotime("+{$months} month"));

        Database::execute("UPDATE servers SET status = 'Assigned', updated_at = NOW() WHERE id = ?", [$serverId]);

        return (int) Database::insert(
            "INSERT INTO server_assignments (server_id, user_id, order_id, status, assigned_at, expiration_date, updated_at)
            VALUES (?, ?, ?, 'Active', NOW(), ?, NOW())
            ON DUPLICATE KEY UPDATE status = 'Active', expiration_date = ?, updated_at = NOW()",
            [$serverId, $userId, $orderId, $expDate, $expDate]
        );
    }

    public static function getUserServers(int $userId): array {
        $rows = Database::fetchAll(
            "SELECT s.*, sa.id as assignment_id, sa.order_id, sa.status as assignment_status, sa.assigned_at, sa.expiration_date,
                    o.order_number, o.package_name, o.daily_pop, o.monthly_pop
             FROM server_assignments sa
             JOIN servers s ON sa.server_id = s.id
             JOIN orders o ON sa.order_id = o.id
             WHERE sa.user_id = ?
             ORDER BY sa.id DESC",
            [$userId]
        );

        return array_map(function ($row) {
            $row['decrypted_password'] = EncryptionService::decrypt($row['encrypted_password']);
            return $row;
        }, $rows);
    }

    public static function findUserServerDetail(int $serverId, int $userId): ?array {
        $row = Database::fetch(
            "SELECT s.*, sa.id as assignment_id, sa.order_id, sa.status as assignment_status, sa.assigned_at, sa.expiration_date,
                    o.order_number, o.package_name, o.daily_pop, o.monthly_pop
             FROM server_assignments sa
             JOIN servers s ON sa.server_id = s.id
             JOIN orders o ON sa.order_id = o.id
             WHERE sa.server_id = ? AND sa.user_id = ?
             LIMIT 1",
            [$serverId, $userId]
        );

        if ($row) {
            $row['decrypted_password'] = EncryptionService::decrypt($row['encrypted_password']);
        }
        return $row;
    }
}
