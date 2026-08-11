<?php

namespace App\Models;

use App\Services\Database;

class User {
    public static function findById(int $id): ?array {
        return Database::fetch("SELECT * FROM users WHERE id = ?", [$id]);
    }

    public static function findByEmail(string $email): ?array {
        return Database::fetch("SELECT * FROM users WHERE email = ?", [strtolower(trim($email))]);
    }

    public static function create(array $data): int {
        return (int) Database::insert(
            "INSERT INTO users (name, email, phone, address, password_hash, role, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())",
            [
                $data['name'],
                strtolower(trim($data['email'])),
                $data['phone'],
                $data['address'] ?? '',
                password_hash($data['password'], PASSWORD_BCRYPT),
                $data['role'] ?? 'user',
                $data['status'] ?? 'active'
            ]
        );
    }

    public static function updateProfile(int $userId, array $data): bool {
        return Database::execute(
            "UPDATE users SET name = ?, phone = ?, address = ?, updated_at = NOW() WHERE id = ?",
            [$data['name'], $data['phone'], $data['address'], $userId]
        ) >= 0;
    }

    public static function updatePassword(int $userId, string $newPassword): bool {
        return Database::execute(
            "UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?",
            [password_hash($newPassword, PASSWORD_BCRYPT), $userId]
        ) > 0;
    }

    public static function getAll(): array {
        return Database::fetchAll("SELECT * FROM users ORDER BY id DESC");
    }

    public static function countTotal(): int {
        $row = Database::fetch("SELECT COUNT(*) as cnt FROM users WHERE role = 'user'");
        return $row ? (int)$row['cnt'] : 0;
    }
}
