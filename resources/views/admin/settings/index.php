<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="text-white mb-0"><i class="fa-solid fa-gears text-indigo me-2"></i>System Configuration Settings</h3>
        <p class="text-light-silver small mb-0">Manage global website options, automated notification triggers, and SMTP mail dispatch settings</p>
    </div>
</div>

<form method="POST" action="/admin/settings">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- Site & Company Settings -->
        <div class="col-md-6">
            <div class="card-custom p-4 h-100">
                <h5 class="text-white fw-bold mb-3"><i class="fa-solid fa-globe text-indigo me-2"></i>Site & Branding Settings</h5>
                
                <div class="mb-3">
                    <label class="form-label text-white small fw-bold">Platform Name</label>
                    <input type="text" name="settings[site_name]" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($settings['site_name'] ?? 'Server Provisioning Portal') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label text-white small fw-bold">Platform URL</label>
                    <input type="text" name="settings[site_url]" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($settings['site_url'] ?? 'https://mailszo.com') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label text-white small fw-bold">Company Legal Name</label>
                    <input type="text" name="settings[company_name]" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($settings['company_name'] ?? 'Edge-Tec Server Solutions') ?>">
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-white small fw-bold">Support Email</label>
                        <input type="email" name="settings[contact_email]" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($settings['contact_email'] ?? 'support@mailszo.com') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white small fw-bold">Support Phone</label>
                        <input type="text" name="settings[contact_phone]" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($settings['contact_phone'] ?? '+1 800-555-0199') ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label text-white small fw-bold">Currency Code</label>
                        <input type="text" name="settings[currency_code]" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($settings['currency_code'] ?? 'USD') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white small fw-bold">Currency Symbol</label>
                        <input type="text" name="settings[currency_symbol]" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($settings['currency_symbol'] ?? '$') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- SMTP Mail Dispatch Settings -->
        <div class="col-md-6">
            <div class="card-custom p-4 h-100">
                <h5 class="text-white fw-bold mb-3"><i class="fa-solid fa-paper-plane text-indigo me-2"></i>SMTP Mailer Settings</h5>
                
                <div class="row mb-3">
                    <div class="col-md-8">
                        <label class="form-label text-white small fw-bold">SMTP Host Server</label>
                        <input type="text" name="settings[smtp_host]" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($settings['smtp_host'] ?? 'smtp.mailtrap.io') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-white small fw-bold">Port</label>
                        <input type="text" name="settings[smtp_port]" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($settings['smtp_port'] ?? '2525') ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-white small fw-bold">SMTP Username</label>
                        <input type="text" name="settings[smtp_user]" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($settings['smtp_user'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white small fw-bold">SMTP Password</label>
                        <input type="password" name="settings[smtp_pass]" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($settings['smtp_pass'] ?? '') ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white small fw-bold">Encryption Protocol</label>
                    <select name="settings[smtp_encryption]" class="form-select bg-dark text-white border-secondary">
                        <option value="tls" <?= ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS (Recommended)</option>
                        <option value="ssl" <?= ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label text-white small fw-bold">Sender Mail Address</label>
                        <input type="email" name="settings[mail_from_address]" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($settings['mail_from_address'] ?? 'noreply@mailszo.com') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white small fw-bold">Sender Name</label>
                        <input type="text" name="settings[mail_from_name]" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($settings['mail_from_name'] ?? 'Server Operations') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Triggers & Automated Email Toggles -->
        <div class="col-12">
            <div class="card-custom p-4">
                <h5 class="text-white fw-bold mb-3"><i class="fa-solid fa-bell text-indigo me-2"></i>Automated Email Notifications & Event Triggers</h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-check form-switch bg-dark p-3 rounded border border-secondary d-flex justify-content-between align-items-center me-0">
                            <div>
                                <label class="form-check-label text-white fw-bold d-block" for="t1">User Registration Welcome Email</label>
                                <span class="text-light-silver small">Send welcome message when a new customer registers</span>
                            </div>
                            <input class="form-check-input ms-3" type="checkbox" name="settings[notify_user_registration]" value="1" id="t1" <?= !empty($settings['notify_user_registration']) ? 'checked' : '' ?>>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check form-switch bg-dark p-3 rounded border border-secondary d-flex justify-content-between align-items-center me-0">
                            <div>
                                <label class="form-check-label text-white fw-bold d-block" for="t2">Order Submission Confirmation</label>
                                <span class="text-light-silver small">Send confirmation email to customer upon placing an order</span>
                            </div>
                            <input class="form-check-input ms-3" type="checkbox" name="settings[notify_order_submitted]" value="1" id="t2" <?= !empty($settings['notify_order_submitted']) ? 'checked' : '' ?>>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check form-switch bg-dark p-3 rounded border border-secondary d-flex justify-content-between align-items-center me-0">
                            <div>
                                <label class="form-check-label text-white fw-bold d-block" for="t3">Order Approval & Credentials Dispatch</label>
                                <span class="text-light-silver small">Automatically email server credentials to customer on order approval</span>
                            </div>
                            <input class="form-check-input ms-3" type="checkbox" name="settings[notify_order_approved]" value="1" id="t3" <?= !empty($settings['notify_order_approved']) ? 'checked' : '' ?>>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check form-switch bg-dark p-3 rounded border border-secondary d-flex justify-content-between align-items-center me-0">
                            <div>
                                <label class="form-check-label text-white fw-bold d-block" for="t4">Admin New Order Alert</label>
                                <span class="text-light-silver small">Notify administrators when a new server order is submitted</span>
                            </div>
                            <input class="form-check-input ms-3" type="checkbox" name="settings[notify_admin_new_order]" value="1" id="t4" <?= !empty($settings['notify_admin_new_order']) ? 'checked' : '' ?>>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold"><i class="fa-solid fa-save me-1"></i> Save All System Settings</button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/admin.php';
?>
