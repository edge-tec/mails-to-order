<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-white mb-0"><i class="fa-solid fa-gears text-indigo me-2"></i>System & Notification Settings</h3>
</div>

<form method="POST" action="/admin/settings">
    <?= csrf_field() ?>

    <div class="row g-4 mb-4">
        <!-- Site Settings -->
        <div class="col-md-6">
            <div class="card-custom p-4 h-100">
                <h5 class="text-indigo mb-3"><i class="fa-solid fa-globe me-2"></i>Portal Branding Settings</h5>
                <div class="mb-3">
                    <label class="form-label text-white">Website Name</label>
                    <input type="text" name="settings[site_name]" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($settings['site_name'] ?? 'Server Provisioning Portal') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label text-white">Company Name</label>
                    <input type="text" name="settings[company_name]" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($settings['company_name'] ?? 'Edge-Tec Server Solutions') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label text-white">Portal Base URL</label>
                    <input type="url" name="settings[site_url]" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($settings['site_url'] ?? config('app.url')) ?>">
                </div>
            </div>
        </div>

        <!-- Notification Toggles -->
        <div class="col-md-6">
            <div class="card-custom p-4 h-100">
                <h5 class="text-indigo mb-3"><i class="fa-solid fa-bell me-2"></i>Email Notification Controls</h5>
                
                <h6 class="text-white small fw-bold mb-2">Customer Email Alerts</h6>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="settings[notify_user_registration]" value="1" id="n1" <?= ($settings['notify_user_registration'] ?? '1') === '1' ? 'checked' : '' ?>>
                    <label class="form-check-label text-light small" for="n1">User Registration Welcome Email</label>
                </div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="settings[notify_order_submitted]" value="1" id="n2" <?= ($settings['notify_order_submitted'] ?? '1') === '1' ? 'checked' : '' ?>>
                    <label class="form-check-label text-light small" for="n2">Order Receipt Email</label>
                </div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="settings[notify_order_approved]" value="1" id="n3" <?= ($settings['notify_order_approved'] ?? '1') === '1' ? 'checked' : '' ?>>
                    <label class="form-check-label text-light small" for="n3">Order Approval & Credentials Email</label>
                </div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="settings[notify_order_rejected]" value="1" id="n4" <?= ($settings['notify_order_rejected'] ?? '1') === '1' ? 'checked' : '' ?>>
                    <label class="form-check-label text-light small" for="n4">Order Rejection Notification</label>
                </div>

                <hr class="border-secondary my-3">

                <h6 class="text-white small fw-bold mb-2">Administrator Alerts</h6>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="settings[notify_admin_new_order]" value="1" id="n5" <?= ($settings['notify_admin_new_order'] ?? '1') === '1' ? 'checked' : '' ?>>
                    <label class="form-check-label text-light small" for="n5">Alert Admin on New Order Submission</label>
                </div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="settings[notify_admin_new_payment]" value="1" id="n6" <?= ($settings['notify_admin_new_payment'] ?? '1') === '1' ? 'checked' : '' ?>>
                    <label class="form-check-label text-light small" for="n6">Alert Admin on Payment Upload</label>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-primary btn-lg px-4"><i class="fa-solid fa-floppy-disk me-2"></i>Save System Settings</button>
    </div>
</form>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>
