<?php

namespace App\Controllers\Admin;

use App\Models\Order;
use App\Models\User;
use App\Models\Server;
use App\Middleware\AdminMiddleware;
use App\Services\Database;

class AdminDashboardController {

    public function index() {
        AdminMiddleware::handle();
        $stats = Order::getDashboardStats();
        $stats['total_users'] = User::countTotal();

        $recentOrders = array_slice(Order::getAll(), 0, 10);
        $recentLogs = Database::fetchAll("SELECT al.*, u.name as admin_name FROM admin_logs al JOIN users u ON al.admin_id = u.id ORDER BY al.id DESC LIMIT 5");

        view('admin.dashboard', [
            'title' => 'Admin Overview & Statistics',
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'recentLogs' => $recentLogs
        ]);
    }
}
