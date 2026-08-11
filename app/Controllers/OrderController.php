<?php

namespace App\Controllers;

use App\Models\Package;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Middleware\AuthMiddleware;
use App\Services\EmailService;
use Exception;

class OrderController {

    public function selectPackage(int $packageId) {
        AuthMiddleware::handle();
        $package = Package::findById($packageId);

        if (!$package || $package['status'] !== 'active') {
            flash('error', 'The selected package is unavailable.');
            redirect('/packages');
        }

        @session_start();
        $_SESSION['order_draft'] = [
            'package_id' => $package['id'],
            'package_name' => $package['name'],
            'daily_pop' => $package['daily_pop_limit'],
            'monthly_pop' => $package['monthly_pop_limit'],
            'price' => $package['price'],
            'currency' => $package['currency']
        ];

        redirect('/order/step1');
    }

    public function showStep1() {
        AuthMiddleware::handle();
        @session_start();

        if (empty($_SESSION['order_draft'])) {
            flash('error', 'Please select a server package first.');
            redirect('/packages');
        }

        $user = User::findById(auth_user()['id']);
        view('order.step1_customer', [
            'title' => 'Order Step 1 — Customer Information',
            'package' => $_SESSION['order_draft'],
            'user' => $user
        ]);
    }

    public function processStep1() {
        AuthMiddleware::handle();
        verify_csrf();
        @session_start();

        if (empty($_SESSION['order_draft'])) {
            redirect('/packages');
        }

        $name = trim($_POST['customer_name'] ?? '');
        $email = strtolower(trim($_POST['customer_email'] ?? ''));
        $phone = trim($_POST['customer_phone'] ?? '');
        $address = trim($_POST['customer_address'] ?? '');

        if (empty($name) || empty($email) || empty($phone) || empty($address)) {
            flash('error', 'All customer information fields are required.');
            redirect('/order/step1');
        }

        $_SESSION['order_draft']['customer_name'] = $name;
        $_SESSION['order_draft']['customer_email'] = $email;
        $_SESSION['order_draft']['customer_phone'] = $phone;
        $_SESSION['order_draft']['customer_address'] = $address;

        redirect('/order/step2');
    }

    public function showStep2() {
        AuthMiddleware::handle();
        @session_start();

        if (empty($_SESSION['order_draft']) || empty($_SESSION['order_draft']['customer_name'])) {
            redirect('/order/step1');
        }

        $paymentMethods = PaymentMethod::getAllActive();
        view('order.step2_payment', [
            'title' => 'Order Step 2 — Payment Information',
            'package' => $_SESSION['order_draft'],
            'paymentMethods' => $paymentMethods
        ]);
    }

    public function processStep2() {
        AuthMiddleware::handle();
        verify_csrf();
        @session_start();

        if (empty($_SESSION['order_draft'])) {
            redirect('/packages');
        }

        $methodCode = trim($_POST['payment_method_code'] ?? '');
        $trxId = trim($_POST['transaction_id'] ?? '');
        $amount = (float)($_POST['amount'] ?? 0);
        $note = trim($_POST['payment_note'] ?? '');

        $method = PaymentMethod::findByCode($methodCode);
        if (!$method || $method['status'] !== 'active') {
            flash('error', 'Invalid payment method selected.');
            redirect('/order/step2');
        }

        if (empty($trxId) || $amount <= 0) {
            flash('error', 'Please provide a valid Transaction ID and Amount.');
            redirect('/order/step2');
        }

        // Handle screenshot upload securely
        $screenshotPath = null;
        if (isset($_FILES['screenshot']) && $_FILES['screenshot']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['screenshot'];
            $maxSize = 5 * 1024 * 1024; // 5MB limit
            
            if ($file['size'] > $maxSize) {
                flash('error', 'Screenshot file size exceeds 5MB limit.');
                redirect('/order/step2');
            }

            // MIME type check
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            $allowedMimes = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp'
            ];

            if (!isset($allowedMimes[$mime])) {
                flash('error', 'Invalid image format. Allowed formats: JPG, JPEG, PNG, WEBP.');
                redirect('/order/step2');
            }

            $ext = $allowedMimes[$mime];
            $randomName = 'pay_' . bin2hex(random_bytes(16)) . '.' . $ext;
            
            $uploadDir = config('app.upload_path', __DIR__ . '/../../storage/uploads');
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $targetPath = $uploadDir . '/' . $randomName;
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $screenshotPath = $randomName;
            }
        }

        $_SESSION['order_draft']['payment'] = [
            'method_code' => $method['code'],
            'method_name' => $method['name'],
            'transaction_id' => $trxId,
            'amount' => $amount,
            'note' => $note,
            'screenshot_path' => $screenshotPath
        ];

        redirect('/order/step3');
    }

    public function showStep3() {
        AuthMiddleware::handle();
        @session_start();

        if (empty($_SESSION['order_draft']) || empty($_SESSION['order_draft']['payment'])) {
            redirect('/order/step2');
        }

        view('order.step3_confirm', [
            'title' => 'Order Step 3 — Review & Confirm Order',
            'draft' => $_SESSION['order_draft']
        ]);
    }

    public function confirmOrder() {
        AuthMiddleware::handle();
        verify_csrf();
        @session_start();

        if (empty($_SESSION['order_draft']) || empty($_SESSION['order_draft']['payment'])) {
            redirect('/packages');
        }

        $draft = $_SESSION['order_draft'];
        $user = auth_user();

        $customUrl = trim($_POST['custom_server_url'] ?? '');
        $customUser = trim($_POST['custom_email_username'] ?? '');
        $customPass = $_POST['custom_server_password'] ?? '';

        try {
            // Create Order with custom server preferences
            $orderId = Order::create([
                'user_id' => $user['id'],
                'package_id' => $draft['package_id'],
                'package_name' => $draft['package_name'],
                'daily_pop' => $draft['daily_pop'],
                'monthly_pop' => $draft['monthly_pop'],
                'price' => $draft['price'],
                'status' => 'Under Review',
                'customer_name' => $draft['customer_name'],
                'customer_email' => $draft['customer_email'],
                'customer_phone' => $draft['customer_phone'],
                'customer_address' => $draft['customer_address'],
                'custom_server_url' => $customUrl,
                'custom_email_username' => $customUser,
                'custom_server_password' => $customPass,
                'notes' => 'Submitted via checkout'
            ]);

            $order = Order::findById($orderId);

            // Create Payment
            Payment::create([
                'order_id' => $orderId,
                'payment_method_code' => $draft['payment']['method_code'],
                'payment_method_name' => $draft['payment']['method_name'],
                'transaction_id' => $draft['payment']['transaction_id'],
                'amount' => $draft['payment']['amount'],
                'screenshot_path' => $draft['payment']['screenshot_path'],
                'payment_note' => $draft['payment']['note'],
                'status' => 'pending'
            ]);

            // Send Email Notification safely
            try {
                EmailService::sendOrderSubmitted(User::findById($user['id']), $order);
            } catch (\Throwable $te) {
                error_log("Order notification email error: " . $te->getMessage());
            }

            unset($_SESSION['order_draft']);

            view('order.success', [
                'title' => 'Order Submitted Successfully',
                'order' => $order
            ]);

        } catch (Exception $e) {
            error_log("Order confirmation error: " . $e->getMessage());
            flash('error', "Order submission error: " . $e->getMessage());
            redirect('/order/step3');
        }
    }
}
