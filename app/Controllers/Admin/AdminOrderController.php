<?php

namespace App\Controllers\Admin;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Server;
use App\Models\User;
use App\Models\AdminLog;
use App\Middleware\AdminMiddleware;
use App\Services\Database;
use App\Services\EmailService;
use App\Services\EncryptionService;
use Exception;

class AdminOrderController {

    public function index() {
        AdminMiddleware::handle();
        $orders = Order::getAll();

        view('admin.orders.index', [
            'title' => 'Order Management',
            'orders' => $orders
        ]);
    }

    public function show(int $id) {
        AdminMiddleware::handle();
        $order = Order::findById($id);

        if (!$order) {
            flash('error', 'Order not found.');
            redirect('/admin/orders');
        }

        $payment = Payment::findByOrderId($id);
        $availableServers = Server::getAvailable();
        $assignedServer = Database::fetch(
            "SELECT s.*, sa.expiration_date, sa.status as assignment_status FROM server_assignments sa JOIN servers s ON sa.server_id = s.id WHERE sa.order_id = ? LIMIT 1",
            [$id]
        );

        view('admin.orders.show', [
            'title' => "Review Order #{$order['order_number']}",
            'order' => $order,
            'payment' => $payment,
            'availableServers' => $availableServers,
            'assignedServer' => $assignedServer
        ]);
    }

    public function streamScreenshot(int $paymentId) {
        AdminMiddleware::handle();
        $payment = Database::fetch("SELECT * FROM payments WHERE id = ?", [$paymentId]);

        if (!$payment || empty($payment['screenshot_path'])) {
            http_response_code(404);
            die('Screenshot not found.');
        }

        $uploadDir = config('app.upload_path', __DIR__ . '/../../../storage/uploads');
        $filePath = $uploadDir . '/' . basename($payment['screenshot_path']);

        if (!file_exists($filePath)) {
            http_response_code(404);
            die('File does not exist on disk.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        header("Content-Type: {$mime}");
        header("Content-Length: " . filesize($filePath));
        readfile($filePath);
        exit;
    }

    public function approveOrder(int $id) {
        AdminMiddleware::handle();
        verify_csrf();

        $order = Order::findById($id);
        if (!$order || in_array($order['status'], ['Active', 'Approved'])) {
            flash('error', 'Order is either missing or already approved.');
            redirect("/admin/orders/{$id}");
        }

        $serverId = (int)($_POST['server_id'] ?? 0);
        $createMode = $_POST['server_mode'] ?? 'existing'; // 'existing' or 'new'
        $adminNotes = trim($_POST['admin_notes'] ?? '');

        Database::beginTransaction();

        try {
            // Check & Prepare Server
            if ($createMode === 'new') {
                $hostIp = trim($_POST['new_host_ip'] ?? '');
                $sshPort = (int)($_POST['new_ssh_port'] ?? 22);
                $username = trim($_POST['new_username'] ?? 'root');
                $rawPassword = $_POST['new_password'] ?? '';

                if (empty($hostIp) || empty($rawPassword)) {
                    throw new Exception("Please provide Server Host/IP and Password for new server creation.");
                }

                $serverId = Server::create([
                    'host_ip' => $hostIp,
                    'ssh_port' => $sshPort,
                    'username' => $username,
                    'password' => $rawPassword,
                    'server_type' => 'VPS',
                    'location' => trim($_POST['new_location'] ?? 'USA'),
                    'provider' => 'Provisioned Host',
                    'status' => 'Available',
                    'notes' => 'Created during order approval'
                ]);
            }

            $server = Server::findById($serverId);
            if (!$server) {
                throw new Exception("Please select or specify a valid server to assign.");
            }

            // Step 1: Update order status to Provisioning
            Order::updateStatus($id, 'Provisioning', $adminNotes);

            // Step 2: Assign Server to User
            Server::assignToOrder($serverId, (int)$order['user_id'], $id, 1);

            // Step 3: Verify Payment if present
            $payment = Payment::findByOrderId($id);
            if ($payment) {
                Payment::verify((int)$payment['id'], auth_user()['id']);
            }

            // Step 4: Update order status to Active
            Order::updateStatus($id, 'Active', $adminNotes);

            Database::commit();

            // Decrypt password for email notification
            $server['decrypted_password'] = EncryptionService::decrypt($server['encrypted_password']);
            $server['expiration_date'] = date('Y-m-d H:i:s', strtotime('+1 month'));

            // Send Email Notification
            $user = User::findById((int)$order['user_id']);
            EmailService::sendOrderApprovedWithCredentials($user, $order, $server);

            // Audit log
            AdminLog::log('Order Approved', 'Order', $id, "Approved & assigned server {$server['host_ip']} to user {$user['email']}");

            flash('success', "Order #{$order['order_number']} has been approved and server credentials emailed to customer!");
            redirect("/admin/orders/{$id}");

        } catch (Exception $e) {
            Database::rollBack();
            flash('error', "Approval Failed: " . $e->getMessage());
            redirect("/admin/orders/{$id}");
        }
    }

    public function rejectOrder(int $id) {
        AdminMiddleware::handle();
        verify_csrf();

        $order = Order::findById($id);
        if (!$order) {
            flash('error', 'Order not found.');
            redirect('/admin/orders');
        }

        $reason = trim($_POST['rejection_reason'] ?? 'Payment verification failed.');
        Order::updateStatus($id, 'Rejected', $reason);

        $user = User::findById((int)$order['user_id']);
        EmailService::sendOrderRejected($user, $order, $reason);

        AdminLog::log('Order Rejected', 'Order', $id, "Rejected order #{$order['order_number']}. Reason: {$reason}");

        flash('success', "Order #{$order['order_number']} rejected.");
        redirect("/admin/orders/{$id}");
    }
}
