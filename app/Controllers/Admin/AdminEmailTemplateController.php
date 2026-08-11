<?php

namespace App\Controllers\Admin;

use App\Services\Database;
use App\Models\AdminLog;
use App\Middleware\AdminMiddleware;

class AdminEmailTemplateController {

    public function index() {
        AdminMiddleware::handle();
        $templates = Database::fetchAll("SELECT * FROM email_templates ORDER BY id ASC");

        view('admin.email_templates.index', [
            'title' => 'HTML Email Templates',
            'templates' => $templates
        ]);
    }

    public function update(int $id) {
        AdminMiddleware::handle();
        verify_csrf();

        $title = trim($_POST['title'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $body = $_POST['body_html'] ?? '';

        Database::execute(
            "UPDATE email_templates SET title = ?, subject = ?, body_html = ?, updated_at = NOW() WHERE id = ?",
            [$title, $subject, $body, $id]
        );

        AdminLog::log('Email Template Updated', 'EmailTemplate', $id, "Updated template {$title}");

        flash('success', "Email template '{$title}' updated successfully.");
        redirect('/admin/email-templates');
    }
}
