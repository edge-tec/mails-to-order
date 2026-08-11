<?php

namespace App\Controllers\Admin;

use App\Models\PaymentMethod;
use App\Models\AdminLog;
use App\Middleware\AdminMiddleware;

class AdminPaymentController {

    public function index() {
        AdminMiddleware::handle();
        $methods = PaymentMethod::getAll();

        view('admin.payment_methods.index', [
            'title' => 'Payment Method Settings',
            'methods' => $methods
        ]);
    }

    public function store() {
        AdminMiddleware::handle();
        verify_csrf();

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $code = trim($_POST['code'] ?? 'method_' . time());
        $name = trim($_POST['name'] ?? '');
        $type = trim($_POST['type'] ?? 'mobile_wallet');
        $number = trim($_POST['personal_number'] ?? '');
        $currency = trim($_POST['currency'] ?? 'BDT');
        $network = trim($_POST['network'] ?? '');
        $wallet = trim($_POST['wallet_address'] ?? '');
        $instructions = trim($_POST['instructions'] ?? '');
        $status = trim($_POST['status'] ?? 'active');

        if (empty($name)) {
            flash('error', 'Payment method name is required.');
            redirect('/admin/payment-methods');
        }

        PaymentMethod::save([
            'id' => $id,
            'code' => $code,
            'name' => $name,
            'type' => $type,
            'personal_number' => $number,
            'currency' => $currency,
            'network' => $network,
            'wallet_address' => $wallet,
            'instructions' => $instructions,
            'status' => $status
        ]);

        AdminLog::log('Payment Method Saved', 'PaymentMethod', $id, "Saved payment method {$name}");

        flash('success', "Payment method '{$name}' saved successfully.");
        redirect('/admin/payment-methods');
    }

    public function delete(int $id) {
        AdminMiddleware::handle();
        verify_csrf();

        PaymentMethod::delete($id);
        AdminLog::log('Payment Method Deleted', 'PaymentMethod', $id, "Deleted method ID {$id}");

        flash('success', 'Payment method removed.');
        redirect('/admin/payment-methods');
    }
}
