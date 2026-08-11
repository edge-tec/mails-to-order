<?php
ob_start();
?>
<div class="row justify-content-center py-4">
    <div class="col-md-9">
        <div class="card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary">
                <h4 class="text-white mb-0"><i class="fa-solid fa-file-invoice text-indigo me-2"></i>Step 3 — Confirm Order Details</h4>
                <span class="badge bg-primary">Order Progress 3 / 3</span>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card bg-dark border-secondary p-3 h-100">
                        <h6 class="text-indigo border-bottom border-secondary pb-2 mb-3"><i class="fa-solid fa-user me-2"></i>Customer Info</h6>
                        <p class="text-white mb-1"><strong><?= htmlspecialchars($draft['customer_name']) ?></strong></p>
                        <p class="text-white small mb-1"><i class="fa-solid fa-envelope me-1"></i><?= htmlspecialchars($draft['customer_email']) ?></p>
                        <p class="text-white small mb-1"><i class="fa-solid fa-phone me-1"></i><?= htmlspecialchars($draft['customer_phone']) ?></p>
                        <p class="text-white small mb-0"><i class="fa-solid fa-location-dot me-1"></i><?= htmlspecialchars($draft['customer_address']) ?></p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-dark border-secondary p-3 h-100">
                        <h6 class="text-indigo border-bottom border-secondary pb-2 mb-3"><i class="fa-solid fa-server me-2"></i>Package Info</h6>
                        <p class="text-white mb-1"><strong><?= htmlspecialchars($draft['package_name']) ?></strong></p>
                        <p class="text-white small mb-1">Daily POP: <strong><?= number_format($draft['daily_pop']) ?></strong></p>
                        <p class="text-white small mb-1">Monthly POP: <strong><?= number_format($draft['monthly_pop']) ?></strong></p>
                        <p class="text-indigo fw-bold fs-5 mb-0">$<?= number_format($draft['price'], 2) ?>/mo</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-dark border-secondary p-3 h-100">
                        <h6 class="text-indigo border-bottom border-secondary pb-2 mb-3"><i class="fa-solid fa-credit-card me-2"></i>Payment Details</h6>
                        <p class="text-white mb-1">Method: <strong><?= htmlspecialchars($draft['payment']['method_name']) ?></strong></p>
                        <p class="text-white small mb-1">Transaction ID: <span class="text-info font-monospace"><?= htmlspecialchars($draft['payment']['transaction_id']) ?></span></p>
                        <p class="text-white small mb-1">Amount Submitted: <strong>$<?= number_format($draft['payment']['amount'], 2) ?></strong></p>
                        <?php if ($draft['payment']['screenshot_path']): ?>
                            <p class="text-success small mb-0"><i class="fa-solid fa-paperclip me-1"></i> Screenshot attached</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <form method="POST" action="/order/step3">
                <?= csrf_field() ?>

                <!-- Custom Server Specifications Box -->
                <div class="card bg-dark border-indigo p-4 mb-4">
                    <h6 class="text-white fw-bold mb-2"><i class="fa-solid fa-sliders text-indigo me-2"></i>Custom Server Setup Preferences (Optional)</h6>
                    <p class="text-light-silver small mb-3">You can specify your preferred Server Hostname / Domain URL, Email Username, and Server Password below. Administrators will configure your server node accordingly upon order approval.</p>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label text-white small fw-bold">Preferred Server URL / Hostname</label>
                            <input type="text" name="custom_server_url" class="form-control bg-secondary text-white border-secondary" placeholder="e.g. srv1.mybrand.com">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-white small fw-bold">Preferred Email / Username</label>
                            <input type="text" name="custom_email_username" class="form-control bg-secondary text-white border-secondary" placeholder="e.g. admin@mybrand.com">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-white small fw-bold">Preferred Server Password</label>
                            <input type="password" name="custom_server_password" class="form-control bg-secondary text-white border-secondary" placeholder="e.g. SecretPassword123!">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="/order/step2" class="btn btn-outline-secondary">Edit Payment</a>
                    <button type="submit" class="btn btn-success btn-lg px-5"><i class="fa-solid fa-check-double me-2"></i>Confirm & Submit Order</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
