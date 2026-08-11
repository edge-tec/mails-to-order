<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Server;
use App\Models\CustomPackageRequest;
use App\Middleware\AuthMiddleware;
use App\Services\POPTrackingService;
use App\Services\EncryptionService;

class UserDashboardController {

    public function dashboard() {
        AuthMiddleware::handle();
        $user = auth_user();

        $orders = Order::getByUserId($user['id']);
        $servers = Server::getUserServers($user['id']);

        // Calculate aggregate metrics
        $activeCount = count(array_filter($servers, fn($s) => $s['assignment_status'] === 'Active'));
        $latestServer = $servers[0] ?? null;

        $popMetrics = null;
        if ($latestServer) {
            $popMetrics = POPTrackingService::getUsage(
                (int)$latestServer['id'],
                $user['id'],
                (int)$latestServer['daily_pop'],
                (int)$latestServer['monthly_pop']
            );
        }

        view('user.dashboard', [
            'title' => 'Customer Dashboard',
            'user' => $user,
            'orders' => $orders,
            'servers' => $servers,
            'activeCount' => $activeCount,
            'latestServer' => $latestServer,
            'popMetrics' => $popMetrics
        ]);
    }

    public function orders() {
        AuthMiddleware::handle();
        $user = auth_user();
        $orders = Order::getByUserId($user['id']);

        view('user.orders', [
            'title' => 'My Orders',
            'orders' => $orders
        ]);
    }

    public function orderDetail(int $id) {
        AuthMiddleware::handle();
        $user = auth_user();
        $order = Order::findById($id);

        if (!$order || $order['user_id'] != $user['id']) {
            flash('error', 'Order not found.');
            redirect('/orders');
        }

        view('user.order_detail', [
            'title' => "Order #{$order['order_number']}",
            'order' => $order
        ]);
    }

    public function servers() {
        AuthMiddleware::handle();
        $user = auth_user();
        $servers = Server::getUserServers($user['id']);

        view('user.servers', [
            'title' => 'My Provisioned Servers',
            'servers' => $servers
        ]);
    }

    public function serverDetail(int $id) {
        AuthMiddleware::handle();
        $user = auth_user();
        $server = Server::findUserServerDetail($id, $user['id']);

        if (!$server) {
            flash('error', 'Server details not found or access denied.');
            redirect('/servers');
        }

        $popMetrics = POPTrackingService::getUsage(
            (int)$server['id'],
            $user['id'],
            (int)$server['daily_pop'],
            (int)$server['monthly_pop']
        );

        view('user.server_detail', [
            'title' => "Server — {$server['host_ip']}",
            'server' => $server,
            'popMetrics' => $popMetrics
        ]);
    }

    public function revealPasswordAJAX() {
        AuthMiddleware::handle();
        verify_csrf();
        $user = auth_user();

        $serverId = (int)($_POST['server_id'] ?? 0);
        $server = Server::findUserServerDetail($serverId, $user['id']);

        if (!$server) {
            json_response(['success' => false, 'message' => 'Unauthorized or invalid server.'], 403);
        }

        json_response([
            'success' => true,
            'password' => $server['decrypted_password']
        ]);
    }

    public function profile() {
        AuthMiddleware::handle();
        $user = User::findById(auth_user()['id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            $name = trim($_POST['name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');

            if (empty($name) || empty($phone)) {
                flash('error', 'Name and Phone Number are required.');
                redirect('/profile');
            }

            User::updateProfile($user['id'], [
                'name' => $name,
                'phone' => $phone,
                'address' => $address
            ]);

            // Update session user name
            @session_start();
            $_SESSION['user']['name'] = $name;

            flash('success', 'Profile updated successfully.');
            redirect('/profile');
        }

        view('user.profile', [
            'title' => 'My Account Profile',
            'user' => $user
        ]);
    }

    public function changePassword() {
        AuthMiddleware::handle();
        $user = User::findById(auth_user()['id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (!password_verify($currentPassword, $user['password_hash'])) {
                flash('error', 'Current password is incorrect.');
                redirect('/security');
            }

            if (strlen($newPassword) < 6) {
                flash('error', 'New password must be at least 6 characters.');
                redirect('/security');
            }

            if ($newPassword !== $confirmPassword) {
                flash('error', 'New passwords do not match.');
                redirect('/security');
            }

            User::updatePassword($user['id'], $newPassword);
            flash('success', 'Password updated successfully.');
            redirect('/security');
        }

        view('user.change_password', ['title' => 'Change Account Password']);
    }

    public function customPackageForm() {
        AuthMiddleware::handle();
        view('user.custom_package_request', ['title' => 'Request Custom Server Package']);
    }

    public function processCustomPackage() {
        AuthMiddleware::handle();
        verify_csrf();
        $user = auth_user();

        $dailyPop = (int)($_POST['required_daily_pop'] ?? 0);
        $monthlyPop = (int)($_POST['required_monthly_pop'] ?? 0);
        $location = trim($_POST['preferred_location'] ?? '');
        $requirements = trim($_POST['additional_requirements'] ?? '');
        $contact = trim($_POST['contact_info'] ?? $user['email']);

        if ($dailyPop <= 0 || empty($location)) {
            flash('error', 'Please fill in required daily POP capacity and location preference.');
            redirect('/custom-package-request');
        }

        CustomPackageRequest::create([
            'user_id' => $user['id'],
            'required_daily_pop' => $dailyPop,
            'required_monthly_pop' => $monthlyPop,
            'preferred_location' => $location,
            'additional_requirements' => $requirements,
            'contact_info' => $contact
        ]);

        flash('success', 'Custom package request submitted! Our sales engineering team will review and quote your package shortly.');
        redirect('/dashboard');
    }
}
