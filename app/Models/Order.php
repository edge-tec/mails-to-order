<?php

namespace App\Models;

use App\Services\Database;

class Order {
    public static function generateOrderNumber(): string {
        $prefix = 'SRV-' . date('Ymd') . '-';
        $seq = rand(10000, 99999);
        return $prefix . $seq;
    }

    public static function ensureCustomColumns(): void {
        static $checked = false;
        if ($checked) return;
        $checked = true;

        try {
            $cols = Database::fetchAll("SHOW COLUMNS FROM orders");
            $colNames = array_column($cols, 'Field');
            
            if (!in_array('admin_notes', $colNames)) {
                Database::execute("ALTER TABLE orders ADD COLUMN `admin_notes` TEXT NULL");
            }
            if (!in_array('custom_server_url', $colNames)) {
                Database::execute("ALTER TABLE orders ADD COLUMN `custom_server_url` VARCHAR(255) NULL");
            }
            if (!in_array('custom_email_username', $colNames)) {
                Database::execute("ALTER TABLE orders ADD COLUMN `custom_email_username` VARCHAR(150) NULL");
            }
            if (!in_array('custom_server_password', $colNames)) {
                Database::execute("ALTER TABLE orders ADD COLUMN `custom_server_password` VARCHAR(255) NULL");
            }
        } catch (\Throwable $e) {
            // ignore schema check errors
        }
    }

    public static function create(array $data): int {
        self::ensureCustomColumns();
        $orderNumber = self::generateOrderNumber();
        return (int) Database::insert(
            "INSERT INTO orders (order_number, user_id, package_id, package_name, daily_pop, monthly_pop, price, status, customer_name, customer_email, customer_phone, customer_address, custom_server_url, custom_email_username, custom_server_password, admin_notes, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())",
            [
                $orderNumber,
                (int)$data['user_id'],
                !empty($data['package_id']) ? (int)$data['package_id'] : null,
                $data['package_name'],
                (int)$data['daily_pop'],
                (int)$data['monthly_pop'],
                (float)$data['price'],
                $data['status'] ?? 'Pending Payment',
                $data['customer_name'],
                $data['customer_email'],
                $data['customer_phone'],
                $data['customer_address'],
                $data['custom_server_url'] ?? null,
                $data['custom_email_username'] ?? null,
                $data['custom_server_password'] ?? null,
                $data['admin_notes'] ?? $data['notes'] ?? ''
            ]
        );
    }

    public static function findById(int $id): ?array {
        self::ensureCustomColumns();
        return Database::fetch("SELECT o.*, u.email as user_email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?", [$id]);
    }

    public static function findByOrderNumber(string $orderNumber): ?array {
        self::ensureCustomColumns();
        return Database::fetch("SELECT * FROM orders WHERE order_number = ?", [$orderNumber]);
    }

    public static function getByUserId(int $userId): array {
        self::ensureCustomColumns();
        return Database::fetchAll("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC", [$userId]);
    }

    public static function getAll(): array {
        self::ensureCustomColumns();
        return Database::fetchAll(
            "SELECT o.*, p.transaction_id, p.payment_method_name, p.screenshot_path, p.amount as paid_amount
             FROM orders o
             LEFT JOIN payments p ON o.id = p.order_id
             ORDER BY o.id DESC"
        );
    }

    public static function updateStatus(int $orderId, string $status, string $adminNotes = ''): bool {
        return Database::execute(
            "UPDATE orders SET status = ?, admin_notes = ?, updated_at = NOW() WHERE id = ?",
            [$status, $adminNotes, $orderId]
        ) >= 0;
    }

    public static function getDashboardStats(): array {
        $totalOrders = (int)(Database::fetch("SELECT COUNT(*) as c FROM orders")['c'] ?? 0);
        $pendingOrders = (int)(Database::fetch("SELECT COUNT(*) as c FROM orders WHERE status = 'Pending Payment'")['c'] ?? 0);
        $underReview = (int)(Database::fetch("SELECT COUNT(*) as c FROM orders WHERE status IN ('Payment Submitted', 'Under Review')")['c'] ?? 0);
        $approvedOrders = (int)(Database::fetch("SELECT COUNT(*) as c FROM orders WHERE status IN ('Approved', 'Provisioning', 'Active')")['c'] ?? 0);
        $activeServers = (int)(Database::fetch("SELECT COUNT(*) as c FROM server_assignments WHERE status = 'Active'")['c'] ?? 0);
        $suspendedServers = (int)(Database::fetch("SELECT COUNT(*) as c FROM server_assignments WHERE status = 'Suspended'")['c'] ?? 0);
        $revenue = (float)(Database::fetch("SELECT SUM(price) as rev FROM orders WHERE status IN ('Approved', 'Provisioning', 'Active')")['rev'] ?? 0);

        return [
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'under_review' => $underReview,
            'approved_orders' => $approvedOrders,
            'active_servers' => $activeServers,
            'suspended_servers' => $suspendedServers,
            'total_revenue' => $revenue
        ];
    }
}
