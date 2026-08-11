<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Package;
use App\Models\Order;
use App\Models\Server;
use App\Services\POPTrackingService;

class ApiController {

    private function authenticateApi(): array {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            $token = $matches[1];
            // Decode or verify basic session/token
            $parts = explode(':', base64_decode($token));
            if (count($parts) === 2) {
                $user = User::findByEmail($parts[0]);
                if ($user && password_verify($parts[1], $user['password_hash'])) {
                    return $user;
                }
            }
        }

        // Fallback to session auth if available
        if (auth_user()) {
            return User::findById(auth_user()['id']);
        }

        json_response(['error' => 'Unauthorized access. Valid API Bearer token required.'], 401);
        exit;
    }

    public function login() {
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $email = strtolower(trim($data['email'] ?? ''));
        $password = $data['password'] ?? '';

        $user = User::findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            json_response(['error' => 'Invalid login credentials.'], 401);
        }

        $token = base64_encode("{$email}:{$password}");

        json_response([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ]);
    }

    public function packages() {
        $packages = Package::getAllActive();
        json_response(['success' => true, 'data' => $packages]);
    }

    public function orders() {
        $user = $this->authenticateApi();
        $orders = is_admin() ? Order::getAll() : Order::getByUserId($user['id']);
        json_response(['success' => true, 'data' => $orders]);
    }

    public function orderDetail(int $id) {
        $user = $this->authenticateApi();
        $order = Order::findById($id);

        if (!$order || (!is_admin() && $order['user_id'] != $user['id'])) {
            json_response(['error' => 'Order not found or permission denied.'], 404);
        }

        json_response(['success' => true, 'data' => $order]);
    }

    public function servers() {
        $user = $this->authenticateApi();
        $servers = is_admin() ? Server::getAll() : Server::getUserServers($user['id']);
        json_response(['success' => true, 'data' => $servers]);
    }

    public function serverDetail(int $id) {
        $user = $this->authenticateApi();
        $server = Server::findUserServerDetail($id, $user['id']);

        if (!$server && !is_admin()) {
            json_response(['error' => 'Server not found.'], 404);
        }

        if (is_admin()) {
            $server = Server::findById($id);
        }

        json_response(['success' => true, 'data' => $server]);
    }

    public function usage(int $serverId) {
        $user = $this->authenticateApi();
        $server = Server::findUserServerDetail($serverId, $user['id']);

        if (!$server && !is_admin()) {
            json_response(['error' => 'Server not found.'], 404);
        }

        $popMetrics = POPTrackingService::getUsage(
            $serverId,
            $user['id'],
            (int)($server['daily_pop'] ?? 500),
            (int)($server['monthly_pop'] ?? 7000)
        );

        json_response(['success' => true, 'data' => $popMetrics]);
    }
}
