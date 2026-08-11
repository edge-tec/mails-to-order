<?php

namespace App\Middleware;

class AdminMiddleware {
    public static function handle(): void {
        AuthMiddleware::handle();

        $user = auth_user();
        if (!$user || !in_array($user['role'], ['admin', 'super_admin'])) {
            http_response_code(403);
            flash('error', 'Access denied. Administrator privileges required.');
            redirect('/dashboard');
        }
    }
}
