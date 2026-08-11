<?php

namespace App\Controllers;

use App\Models\User;
use App\Middleware\RateLimitMiddleware;
use App\Services\EmailService;
use App\Services\Database;

class AuthController {
    public function showRegister() {
        if (auth_user()) redirect('/dashboard');
        view('auth.register', ['title' => 'Create Account']);
    }

    public function register() {
        verify_csrf();
        
        $name = trim($_POST['name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($name) || empty($email) || empty($phone) || empty($password)) {
            flash('error', 'All fields are required.');
            redirect('/register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Please enter a valid email address.');
            redirect('/register');
        }

        if (strlen($password) < 6) {
            flash('error', 'Password must be at least 6 characters.');
            redirect('/register');
        }

        if ($password !== $confirmPassword) {
            flash('error', 'Passwords do not match.');
            redirect('/register');
        }

        if (User::findByEmail($email)) {
            flash('error', 'An account with this email already exists.');
            redirect('/register');
        }

        $userId = User::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'role' => 'user'
        ]);

        $user = User::findById($userId);
        
        @session_start();
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        // Send Welcome Email
        EmailService::sendRegistrationWelcome($user);

        flash('success', 'Registration successful! Welcome to your dashboard.');
        redirect('/dashboard');
    }

    public function showLogin() {
        if (auth_user()) redirect('/dashboard');
        view('auth.login', ['title' => 'Sign In']);
    }

    public function login() {
        verify_csrf();
        RateLimitMiddleware::handle('login', 5, 15);

        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            flash('error', 'Please fill in both email and password.');
            redirect('/login');
        }

        $user = User::findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            RateLimitMiddleware::increment('login');
            flash('error', 'Invalid email or password.');
            redirect('/login');
        }

        if ($user['status'] !== 'active') {
            flash('error', 'Your account has been suspended or deactivated.');
            redirect('/login');
        }

        RateLimitMiddleware::clear('login');

        // Session regeneration
        @session_start();
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        flash('success', "Welcome back, {$user['name']}!");
        if (in_array($user['role'], ['admin', 'super_admin'])) {
            redirect('/admin');
        }
        redirect('/dashboard');
    }

    public function logout() {
        @session_start();
        unset($_SESSION['user']);
        session_destroy();
        @session_start();
        flash('success', 'You have been logged out successfully.');
        redirect('/login');
    }

    public function showForgotPassword() {
        view('auth.forgot_password', ['title' => 'Forgot Password']);
    }

    public function sendResetLink() {
        verify_csrf();
        $email = strtolower(trim($_POST['email'] ?? ''));
        $user = User::findByEmail($email);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

            Database::execute("DELETE FROM password_resets WHERE email = ?", [$email]);
            Database::insert(
                "INSERT INTO password_resets (email, token, expires_at, created_at) VALUES (?, ?, ?, NOW())",
                [$email, $token, $expiresAt]
            );

            $resetUrl = url('/reset-password?token=' . $token . '&email=' . urlencode($email));
            $html = "<p>Hello {$user['name']},</p><p>We received a request to reset your password. Click below to proceed:</p><p><a href='{$resetUrl}' class='btn'>Reset Password</a></p>";
            EmailService::send($email, $user['name'], 'Password Reset Request', $html, 'password_reset');
        }

        flash('success', 'If an account exists with that email, a password reset link has been sent.');
        redirect('/forgot-password');
    }

    public function showResetPassword() {
        $token = $_GET['token'] ?? '';
        $email = $_GET['email'] ?? '';
        view('auth.reset_password', ['title' => 'Reset Password', 'token' => $token, 'email' => $email]);
    }

    public function resetPassword() {
        verify_csrf();
        $token = $_POST['token'] ?? '';
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($password) || strlen($password) < 6) {
            flash('error', 'Password must be at least 6 characters.');
            redirect("/reset-password?token={$token}&email=" . urlencode($email));
        }

        if ($password !== $confirmPassword) {
            flash('error', 'Passwords do not match.');
            redirect("/reset-password?token={$token}&email=" . urlencode($email));
        }

        $row = Database::fetch(
            "SELECT * FROM password_resets WHERE email = ? AND token = ? AND expires_at > NOW() LIMIT 1",
            [$email, $token]
        );

        if (!$row) {
            flash('error', 'Invalid or expired password reset token.');
            redirect('/forgot-password');
        }

        $user = User::findByEmail($email);
        if ($user) {
            User::updatePassword($user['id'], $password);
            Database::execute("DELETE FROM password_resets WHERE email = ?", [$email]);
            flash('success', 'Your password has been reset successfully. You can now log in.');
            redirect('/login');
        }

        redirect('/login');
    }
}
