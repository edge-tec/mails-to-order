<?php

namespace App\Models;

use App\Services\Database;

class PaymentMethod {
    public static function getAllActive(): array {
        return Database::fetchAll("SELECT * FROM payment_methods WHERE status = 'active' ORDER BY id ASC");
    }

    public static function getAll(): array {
        return Database::fetchAll("SELECT * FROM payment_methods ORDER BY id ASC");
    }

    public static function findByCode(string $code): ?array {
        return Database::fetch("SELECT * FROM payment_methods WHERE code = ?", [$code]);
    }

    public static function findById(int $id): ?array {
        return Database::fetch("SELECT * FROM payment_methods WHERE id = ?", [$id]);
    }

    public static function save(array $data): int {
        if (!empty($data['id'])) {
            Database::execute(
                "UPDATE payment_methods SET name = ?, type = ?, personal_number = ?, currency = ?, network = ?, wallet_address = ?, qr_code_image = ?, instructions = ?, status = ?, updated_at = NOW() WHERE id = ?",
                [
                    $data['name'],
                    $data['type'],
                    $data['personal_number'] ?? null,
                    $data['currency'] ?? null,
                    $data['network'] ?? null,
                    $data['wallet_address'] ?? null,
                    $data['qr_code_image'] ?? null,
                    $data['instructions'] ?? '',
                    $data['status'] ?? 'active',
                    (int)$data['id']
                ]
            );
            return (int)$data['id'];
        }

        return (int) Database::insert(
            "INSERT INTO payment_methods (code, name, type, personal_number, currency, network, wallet_address, qr_code_image, instructions, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())",
            [
                $data['code'],
                $data['name'],
                $data['type'],
                $data['personal_number'] ?? null,
                $data['currency'] ?? null,
                $data['network'] ?? null,
                $data['wallet_address'] ?? null,
                $data['qr_code_image'] ?? null,
                $data['instructions'] ?? '',
                $data['status'] ?? 'active'
            ]
        );
    }

    public static function delete(int $id): bool {
        return Database::execute("DELETE FROM payment_methods WHERE id = ?", [$id]) > 0;
    }
}
