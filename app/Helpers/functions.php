<?php

if (!function_exists('env')) {
    function env($key, $default = null) {
        $val = getenv($key);
        if ($val === false) {
            $val = $_ENV[$key] ?? $_SERVER[$key] ?? null;
        }
        if ($val === null) {
            return $default;
        }
        switch (strtolower((string) $val)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }
        return $val;
    }
}

if (!function_exists('config')) {
    function config($key, $default = null) {
        static $configs = [];
        $parts = explode('.', $key);
        $file = $parts[0];
        
        if (!isset($configs[$file])) {
            $path = __DIR__ . '/../../config/' . $file . '.php';
            if (file_exists($path)) {
                $configs[$file] = require $path;
            } else {
                $configs[$file] = [];
            }
        }
        
        $value = $configs[$file];
        for ($i = 1; $i < count($parts); $i++) {
            if (is_array($value) && array_key_exists($parts[$i], $value)) {
                $value = $value[$parts[$i]];
            } else {
                return $default;
            }
        }
        return $value;
    }
}

if (!function_exists('url')) {
    function url($path = '') {
        $baseUrl = rtrim(config('app.url', 'http://localhost:8000'), '/');
        $cleanPath = '/' . ltrim($path, '/');
        return $baseUrl . ($cleanPath === '/' ? '' : $cleanPath);
    }
}

if (!function_exists('asset')) {
    function asset($path) {
        return url('/assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('sanitize')) {
    function sanitize($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = sanitize($value);
            }
            return $data;
        }
        return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field() {
        return '<input type="hidden" name="_csrf_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('verify_csrf')) {
    function verify_csrf() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $token = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!$token || !hash_equals($_SESSION['_csrf_token'] ?? '', $token)) {
            http_response_code(403);
            die('CSRF Token verification failed.');
        }
    }
}

if (!function_exists('redirect')) {
    function redirect($path) {
        $target = (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) ? $path : url($path);
        header('Location: ' . $target);
        exit;
    }
}

if (!function_exists('json_response')) {
    function json_response($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

if (!function_exists('view')) {
    function view($viewPath, $data = []) {
        extract($data);
        $file = __DIR__ . '/../../resources/views/' . str_replace('.', '/', $viewPath) . '.php';
        if (!file_exists($file)) {
            throw new Exception("View file [{$viewPath}] not found at {$file}");
        }
        require $file;
    }
}

if (!function_exists('flash')) {
    function flash($key, $message = null) {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        if ($message !== null) {
            $_SESSION['_flash'][$key] = $message;
        } else {
            $msg = $_SESSION['_flash'][$key] ?? null;
            unset($_SESSION['_flash'][$key]);
            return $msg;
        }
    }
}

if (!function_exists('old')) {
    function old($key, $default = '') {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $value = $_SESSION['_old_input'][$key] ?? $default;
        return sanitize($value);
    }
}

if (!function_exists('auth_user')) {
    function auth_user() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        return $_SESSION['user'] ?? null;
    }
}

if (!function_exists('is_admin')) {
    function is_admin() {
        $user = auth_user();
        return $user && in_array($user['role'], ['admin', 'super_admin']);
    }
}

if (!function_exists('is_super_admin')) {
    function is_super_admin() {
        $user = auth_user();
        return $user && $user['role'] === 'super_admin';
    }
}

if (!function_exists('format_currency')) {
    function format_currency($amount, $currency = 'USD') {
        if ($currency === 'BDT') {
            return '৳' . number_format($amount, 2);
        }
        return '$' . number_format($amount, 2);
    }
}
