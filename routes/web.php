<?php

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\OrderController;
use App\Controllers\UserDashboardController;
use App\Controllers\Admin\AdminDashboardController;
use App\Controllers\Admin\AdminOrderController;
use App\Controllers\Admin\AdminServerController;
use App\Controllers\Admin\AdminPackageController;
use App\Controllers\Admin\AdminPaymentController;
use App\Controllers\Admin\AdminCustomPackageController;
use App\Controllers\Admin\AdminEmailTemplateController;
use App\Controllers\Admin\AdminSettingsController;
use App\Controllers\Admin\AdminAuditLogController;
use App\Controllers\Admin\AdminUserController;
use App\Controllers\ApiController;

return [
    // Public Routes
    'GET /' => [HomeController::class, 'index'],
    'GET /packages' => [HomeController::class, 'packages'],
    'GET /contact' => [HomeController::class, 'contact'],
    'POST /contact' => [HomeController::class, 'contact'],
    'GET /terms' => [HomeController::class, 'terms'],

    // Auth Routes
    'GET /register' => [AuthController::class, 'showRegister'],
    'POST /register' => [AuthController::class, 'register'],
    'GET /login' => [AuthController::class, 'showLogin'],
    'POST /login' => [AuthController::class, 'login'],
    'GET /logout' => [AuthController::class, 'logout'],
    'POST /logout' => [AuthController::class, 'logout'],
    'GET /forgot-password' => [AuthController::class, 'showForgotPassword'],
    'POST /forgot-password' => [AuthController::class, 'sendResetLink'],
    'GET /reset-password' => [AuthController::class, 'showResetPassword'],
    'POST /reset-password' => [AuthController::class, 'resetPassword'],

    // Checkout Flow Routes
    'GET /order/package/{id}' => [OrderController::class, 'selectPackage'],
    'GET /order/step1' => [OrderController::class, 'showStep1'],
    'POST /order/step1' => [OrderController::class, 'processStep1'],
    'GET /order/step2' => [OrderController::class, 'showStep2'],
    'POST /order/step2' => [OrderController::class, 'processStep2'],
    'GET /order/step3' => [OrderController::class, 'showStep3'],
    'POST /order/step3' => [OrderController::class, 'confirmOrder'],

    // User Dashboard Routes
    'GET /dashboard' => [UserDashboardController::class, 'dashboard'],
    'GET /orders' => [UserDashboardController::class, 'orders'],
    'GET /orders/{id}' => [UserDashboardController::class, 'orderDetail'],
    'GET /servers' => [UserDashboardController::class, 'servers'],
    'GET /servers/{id}' => [UserDashboardController::class, 'serverDetail'],
    'POST /ajax/reveal-password' => [UserDashboardController::class, 'revealPasswordAJAX'],
    'GET /profile' => [UserDashboardController::class, 'profile'],
    'POST /profile' => [UserDashboardController::class, 'profile'],
    'GET /security' => [UserDashboardController::class, 'changePassword'],
    'POST /security' => [UserDashboardController::class, 'changePassword'],
    'GET /custom-package-request' => [UserDashboardController::class, 'customPackageForm'],
    'POST /custom-package-request' => [UserDashboardController::class, 'processCustomPackage'],

    // Admin Area Routes
    'GET /admin' => [AdminDashboardController::class, 'index'],
    
    // Admin Users & Impersonation
    'GET /admin/users' => [AdminUserController::class, 'index'],
    'POST /admin/users/{id}/impersonate' => [AdminUserController::class, 'impersonate'],
    'GET /admin/impersonate/stop' => [AdminUserController::class, 'stopImpersonate'],
    'POST /admin/impersonate/stop' => [AdminUserController::class, 'stopImpersonate'],

    // Admin Orders
    'GET /admin/orders' => [AdminOrderController::class, 'index'],
    'GET /admin/orders/{id}' => [AdminOrderController::class, 'show'],
    'GET /admin/payments/screenshot/{id}' => [AdminOrderController::class, 'streamScreenshot'],
    'POST /admin/orders/{id}/approve' => [AdminOrderController::class, 'approveOrder'],
    'POST /admin/orders/{id}/reject' => [AdminOrderController::class, 'rejectOrder'],

    // Admin Servers
    'GET /admin/servers' => [AdminServerController::class, 'index'],
    'GET /admin/servers/create' => [AdminServerController::class, 'create'],
    'POST /admin/servers/create' => [AdminServerController::class, 'store'],
    'GET /admin/servers/{id}/edit' => [AdminServerController::class, 'edit'],
    'POST /admin/servers/{id}/edit' => [AdminServerController::class, 'update'],
    'POST /admin/servers/{id}/status/{status}' => [AdminServerController::class, 'toggleStatus'],

    // Admin Packages
    'GET /admin/packages' => [AdminPackageController::class, 'index'],
    'POST /admin/packages/create' => [AdminPackageController::class, 'store'],
    'POST /admin/packages/{id}/edit' => [AdminPackageController::class, 'update'],
    'POST /admin/packages/{id}/delete' => [AdminPackageController::class, 'delete'],

    // Admin Payment Methods
    'GET /admin/payment-methods' => [AdminPaymentController::class, 'index'],
    'POST /admin/payment-methods/save' => [AdminPaymentController::class, 'store'],
    'POST /admin/payment-methods/{id}/delete' => [AdminPaymentController::class, 'delete'],

    // Admin Custom Package Requests
    'GET /admin/custom-packages' => [AdminCustomPackageController::class, 'index'],
    'POST /admin/custom-packages/{id}/quote' => [AdminCustomPackageController::class, 'updateQuote'],
    'POST /admin/custom-packages/{id}/convert' => [AdminCustomPackageController::class, 'convertToOrder'],

    // Admin Email Templates
    'GET /admin/email-templates' => [AdminEmailTemplateController::class, 'index'],
    'POST /admin/email-templates/{id}/edit' => [AdminEmailTemplateController::class, 'update'],

    // Admin Settings & Audit Logs
    'GET /admin/settings' => [AdminSettingsController::class, 'index'],
    'POST /admin/settings' => [AdminSettingsController::class, 'update'],
    'GET /admin/audit-logs' => [AdminAuditLogController::class, 'index'],

    // API Routes
    'POST /api/login' => [ApiController::class, 'login'],
    'GET /api/packages' => [ApiController::class, 'packages'],
    'GET /api/orders' => [ApiController::class, 'orders'],
    'GET /api/orders/{id}' => [ApiController::class, 'orderDetail'],
    'GET /api/servers' => [ApiController::class, 'servers'],
    'GET /api/servers/{id}' => [ApiController::class, 'serverDetail'],
    'GET /api/usage/{id}' => [ApiController::class, 'usage']
];
