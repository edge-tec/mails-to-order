<?php

namespace App\Models;

use App\Services\Database;

class Package {
    public static function getAllActive(): array {
        return Database::fetchAll("SELECT * FROM packages WHERE status = 'active' ORDER BY sort_order ASC, id ASC");
    }

    public static function getAll(): array {
        return Database::fetchAll("SELECT * FROM packages ORDER BY sort_order ASC, id ASC");
    }

    public static function findById(int $id): ?array {
        return Database::fetch("SELECT * FROM packages WHERE id = ?", [$id]);
    }

    public static function create(array $data): int {
        return (int) Database::insert(
            "INSERT INTO packages (name, type, description, daily_pop_limit, monthly_pop_limit, price, currency, status, sort_order, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())",
            [
                $data['name'],
                $data['type'] ?? 'standard',
                $data['description'] ?? '',
                (int)$data['daily_pop_limit'],
                (int)$data['monthly_pop_limit'],
                (float)$data['price'],
                $data['currency'] ?? 'USD',
                $data['status'] ?? 'active',
                (int)($data['sort_order'] ?? 0)
            ]
        );
    }

    public static function update(int $id, array $data): bool {
        return Database::execute(
            "UPDATE packages SET name = ?, type = ?, description = ?, daily_pop_limit = ?, monthly_pop_limit = ?, price = ?, currency = ?, status = ?, sort_order = ?, updated_at = NOW() WHERE id = ?",
            [
                $data['name'],
                $data['type'] ?? 'standard',
                $data['description'] ?? '',
                (int)$data['daily_pop_limit'],
                (int)$data['monthly_pop_limit'],
                (float)$data['price'],
                $data['currency'] ?? 'USD',
                $data['status'] ?? 'active',
                (int)($data['sort_order'] ?? 0),
                $id
            ]
        ) >= 0;
    }

    public static function delete(int $id): bool {
        return Database::execute("DELETE FROM packages WHERE id = ?", [$id]) > 0;
    }
}
