<?php

namespace App\Controllers\Admin;

use App\Models\CustomPackageRequest;
use App\Models\Order;
use App\Models\User;
use App\Models\AdminLog;
use App\Middleware\AdminMiddleware;
use App\Services\EmailService;

class AdminCustomPackageController {

    public function index() {
        AdminMiddleware::handle();
        $requests = CustomPackageRequest::getAll();

        view('admin.custom_requests.index', [
            'title' => 'Custom Package Requests',
            'requests' => $requests
        ]);
    }

    public function updateQuote(int $id) {
        AdminMiddleware::handle();
        verify_csrf();

        $price = (float)($_POST['admin_quote_price'] ?? 0);
        $notes = trim($_POST['admin_notes'] ?? '');

        CustomPackageRequest::updateQuote($id, $price, $notes);
        AdminLog::log('Custom Request Quoted', 'CustomPackageRequest', $id, "Quoted price {$price} for request {$id}");

        flash('success', "Custom request quoted at $" . number_format($price, 2));
        redirect('/admin/custom-packages');
    }

    public function convertToOrder(int $id) {
        AdminMiddleware::handle();
        verify_csrf();

        $req = CustomPackageRequest::findById($id);
        if (!$req || !$req['admin_quote_price']) {
            flash('error', 'Request must be quoted before converting to an order.');
            redirect('/admin/custom-packages');
        }

        $user = User::findById((int)$req['user_id']);

        $orderId = Order::create([
            'user_id' => $req['user_id'],
            'package_id' => null,
            'package_name' => "Custom Package ({$req['required_daily_pop']} POP/day)",
            'daily_pop' => $req['required_daily_pop'],
            'monthly_pop' => $req['required_monthly_pop'],
            'price' => $req['admin_quote_price'],
            'status' => 'Pending Payment',
            'customer_name' => $user['name'],
            'customer_email' => $user['email'],
            'customer_phone' => $user['phone'],
            'customer_address' => $req['preferred_location'],
            'notes' => 'Converted from custom package request'
        ]);

        \App\Services\Database::execute("UPDATE custom_package_requests SET status = 'converted' WHERE id = ?", [$id]);

        AdminLog::log('Custom Request Converted', 'CustomPackageRequest', $id, "Converted request {$id} to Order {$orderId}");

        flash('success', "Custom request converted to Order #{$orderId}. Customer can now make payment.");
        redirect("/admin/orders/{$orderId}");
    }
}
