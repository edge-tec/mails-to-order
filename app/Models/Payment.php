<?php

namespace App\Models;

use App\Services\Database;

class Payment {
    public static function create(array $data): int {
        return (int) Database::insert(
            "INSERT INTO payments (order_id, payment_method_code, payment_method_name, transaction_id, amount, screenshot_path, payment_note, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())",
            [
                (int)$data['order_id'],
                $data['payment_method_code'],
                $data['payment_method_name'],
                $data['transaction_id'],
                (float)$data['amount'],
                $data['screenshot_path'] ?? null,
                $data['payment_note'] ?? '',
                $data['status'] ?? 'pending'
            ]
        );
    }

    public static function findByOrderId(int $orderId): ?array {
        return Database::fetch("SELECT * FROM payments WHERE order_id = ? ORDER BY id DESC LIMIT 1", [$orderId]);
    }

    public static function verify(int $paymentId, int $adminId): bool {
        return Database::execute(
            "UPDATE payments SET status = 'verified', verified_at = NOW(), verified_by = ? WHERE id = ?",
            [$adminId, $paymentId]
        ) > 0;
    }
}
