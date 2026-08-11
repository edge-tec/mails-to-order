<?php

namespace App\Controllers\Admin;

use App\Services\Database;
use App\Models\User;
use App\Models\AdminLog;
use App\Middleware\AdminMiddleware;

class AdminUserController {

    public function index() {
        AdminMiddleware::handle();
        
        $users = Database::fetchAll(
            "SELECT u.*, 
                (SELECT COUNT(*) FROM orders o WHERE o.user_id = u.id) as order_count,
                (SELECT COUNT(*) FROM user_servers us WHERE us.user_id = u.id) as server_count
             FROM users u 
             ORDER BY u.id DESC"
        );

        view('admin.users.index', [
            'title' => 'Registered User Accounts',
            'users' => $users
        ]);
    }

    public function impersonate(int $id) {
        AdminMiddleware::handle();
        verify_csrf();

        $adminUser = auth_user();
        $targetUser = User::findById($id);

        if (!$targetUser) {
            flash('error', 'Target user account not found.');
            redirect('/admin/users');
        }

        // Store current admin ID in session
        $_SESSION['impersonator_admin'] = $adminUser;
        
        // Log in as target user
        $_SESSION['user'] = $targetUser;

        AdminLog::log('User Impersonated', 'User', $id, "Admin {$adminUser['email']} impersonated user {$targetUser['email']}");

        flash('success', "Now impersonating customer: " . htmlspecialchars($targetUser['name']));
        redirect('/dashboard');
    }

    public function stopImpersonate() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        if (!empty($_SESSION['impersonator_admin'])) {
            $adminUser = $_SESSION['impersonator_admin'];
            $_SESSION['user'] = $adminUser;
            unset($_SESSION['impersonator_admin']);

            flash('success', 'Returned to Administrator Account.');
            redirect('/admin/users');
        } else {
            redirect('/dashboard');
        }
    }
}
