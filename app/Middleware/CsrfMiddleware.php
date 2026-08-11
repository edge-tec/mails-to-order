<?php

namespace App\Middleware;

class CsrfMiddleware {
    public static function handle(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'DELETE') {
            verify_csrf();
        }
    }
}
