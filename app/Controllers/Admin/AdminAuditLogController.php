<?php

namespace App\Controllers\Admin;

use App\Models\AdminLog;
use App\Middleware\AdminMiddleware;

class AdminAuditLogController {

    public function index() {
        AdminMiddleware::handle();
        $logs = AdminLog::getAll();

        view('admin.audit_logs.index', [
            'title' => 'Admin Security & Audit Trail',
            'logs' => $logs
        ]);
    }
}
