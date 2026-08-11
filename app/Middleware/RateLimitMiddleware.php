<?php

namespace App\Middleware;

class RateLimitMiddleware {
    public static function handle(string $actionKey = 'global', int $maxAttempts = 5, int $decayMinutes = 15): void {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $key = "_rate_limit_{$actionKey}_" . md5($ip);

        $now = time();
        $rateData = $_SESSION[$key] ?? ['attempts' => 0, 'reset_at' => $now + ($decayMinutes * 60)];

        if ($now > $rateData['reset_at']) {
            $rateData = ['attempts' => 0, 'reset_at' => $now + ($decayMinutes * 60)];
        }

        if ($rateData['attempts'] >= $maxAttempts) {
            $remaining = ceil(($rateData['reset_at'] - $now) / 60);
            flash('error', "Too many attempts. Please try again in {$remaining} minute(s).");
            redirect('/login');
        }

        $_SESSION[$key] = $rateData;
    }

    public static function increment(string $actionKey = 'global'): void {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $key = "_rate_limit_{$actionKey}_" . md5($ip);

        if (isset($_SESSION[$key])) {
            $_SESSION[$key]['attempts']++;
        }
    }

    public static function clear(string $actionKey = 'global'): void {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $key = "_rate_limit_{$actionKey}_" . md5($ip);
        unset($_SESSION[$key]);
    }
}
