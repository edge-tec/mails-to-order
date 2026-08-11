<?php

namespace App\Controllers\Admin;

use App\Services\Database;
use App\Models\AdminLog;
use App\Middleware\AdminMiddleware;

class AdminSettingsController {

    public function index() {
        AdminMiddleware::handle();
        $settingsRaw = Database::fetchAll("SELECT * FROM settings");
        $settings = [];
        foreach ($settingsRaw as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        view('admin.settings.index', [
            'title' => 'System & Notification Settings',
            'settings' => $settings
        ]);
    }

    public function update() {
        AdminMiddleware::handle();
        verify_csrf();

        $postSettings = $_POST['settings'] ?? [];
        
        // Handle checkboxes/toggles
        $toggleKeys = [
            'notify_user_registration', 'notify_order_submitted',
            'notify_order_approved', 'notify_order_rejected',
            'notify_server_activated', 'notify_server_suspended',
            'notify_admin_new_order', 'notify_admin_new_payment'
        ];

        foreach ($toggleKeys as $tk) {
            if (!isset($postSettings[$tk])) {
                $postSettings[$tk] = '0';
            }
        }

        foreach ($postSettings as $key => $val) {
            Database::execute(
                "INSERT INTO settings (setting_key, setting_value, updated_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE setting_value = ?",
                [$key, $val, $val]
            );
        }

        AdminLog::log('Settings Updated', 'Settings', null, "Updated system configuration settings");

        flash('success', 'System settings updated successfully.');
        redirect('/admin/settings');
    }
}
