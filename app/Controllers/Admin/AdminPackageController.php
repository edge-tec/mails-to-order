<?php

namespace App\Controllers\Admin;

use App\Models\Package;
use App\Models\AdminLog;
use App\Middleware\AdminMiddleware;

class AdminPackageController {

    public function index() {
        AdminMiddleware::handle();
        $packages = Package::getAll();

        view('admin.packages.index', [
            'title' => 'Server Packages & Pricing Config',
            'packages' => $packages
        ]);
    }

    public function store() {
        AdminMiddleware::handle();
        verify_csrf();

        $name = trim($_POST['name'] ?? '');
        $type = trim($_POST['type'] ?? 'standard');
        $daily = (int)($_POST['daily_pop_limit'] ?? 0);
        $monthly = (int)($_POST['monthly_pop_limit'] ?? 0);
        $price = (float)($_POST['price'] ?? 0);
        $currency = trim($_POST['currency'] ?? 'USD');
        $description = trim($_POST['description'] ?? '');
        $status = trim($_POST['status'] ?? 'active');
        $sortOrder = (int)($_POST['sort_order'] ?? 0);

        if (empty($name)) {
            flash('error', 'Package name is required.');
            redirect('/admin/packages');
        }

        $id = Package::create([
            'name' => $name,
            'type' => $type,
            'daily_pop_limit' => $daily,
            'monthly_pop_limit' => $monthly,
            'price' => $price,
            'currency' => $currency,
            'description' => $description,
            'status' => $status,
            'sort_order' => $sortOrder
        ]);

        AdminLog::log('Package Created', 'Package', $id, "Added package {$name}");

        flash('success', "Package '{$name}' created.");
        redirect('/admin/packages');
    }

    public function update(int $id) {
        AdminMiddleware::handle();
        verify_csrf();

        $name = trim($_POST['name'] ?? '');
        $type = trim($_POST['type'] ?? 'standard');
        $daily = (int)($_POST['daily_pop_limit'] ?? 0);
        $monthly = (int)($_POST['monthly_pop_limit'] ?? 0);
        $price = (float)($_POST['price'] ?? 0);
        $currency = trim($_POST['currency'] ?? 'USD');
        $description = trim($_POST['description'] ?? '');
        $status = trim($_POST['status'] ?? 'active');
        $sortOrder = (int)($_POST['sort_order'] ?? 0);

        Package::update($id, [
            'name' => $name,
            'type' => $type,
            'daily_pop_limit' => $daily,
            'monthly_pop_limit' => $monthly,
            'price' => $price,
            'currency' => $currency,
            'description' => $description,
            'status' => $status,
            'sort_order' => $sortOrder
        ]);

        AdminLog::log('Package Updated', 'Package', $id, "Updated package {$name}");

        flash('success', "Package '{$name}' updated.");
        redirect('/admin/packages');
    }

    public function delete(int $id) {
        AdminMiddleware::handle();
        verify_csrf();

        Package::delete($id);
        AdminLog::log('Package Deleted', 'Package', $id, "Deleted package ID {$id}");

        flash('success', 'Package deleted.');
        redirect('/admin/packages');
    }
}
