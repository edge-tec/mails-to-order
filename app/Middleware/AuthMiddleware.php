<?php

namespace App\Middleware;

class AuthMiddleware {
    public static function handle(): void {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        if (empty($_SESSION['user'])) {
            flash('error', 'Please log in to access this page.');
            redirect('/login');
        }
    }
}
