<?php

namespace App\Models;

use App\Services\Database;

class CustomPackageRequest {
    public static function create(array $data): int {
        return (int) Database::insert(
            "INSERT INTO custom_package_requests (user_id, required_daily_pop, required_monthly_pop, preferred_location, additional_requirements, contact_info, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW(), NOW())",
            [
                (int)$data['user_id'],
                (int)$data['required_daily_pop'],
                (int)$data['required_monthly_pop'],
                $data['preferred_location'],
                $data['additional_requirements'] ?? '',
                $data['contact_info']
            ]
        );
    }

    public static function getAll(): array {
        return Database::fetchAll(
            "SELECT c.*, u.name as user_name, u.email as user_email
             FROM custom_package_requests c
             JOIN users u ON c.user_id = u.id
             ORDER BY c.id DESC"
        );
    }

    public static function findById(int $id): ?array {
        return Database::fetch(
            "SELECT c.*, u.name as user_name, u.email as user_email
             FROM custom_package_requests c
             JOIN users u ON c.user_id = u.id
             WHERE c.id = ?",
            [$id]
        );
    }

    public static function updateQuote(int $id, float $price, string $notes): bool {
        return Database::execute(
            "UPDATE custom_package_requests SET admin_quote_price = ?, admin_notes = ?, status = 'quoted', updated_at = NOW() WHERE id = ?",
            [$price, $notes, $id]
        ) > 0;
    }
}
