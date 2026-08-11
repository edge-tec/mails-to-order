<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="text-white mb-0">Review Order #<?= htmlspecialchars($order['order_number']) ?></h3>
        <span class="badge badge-status badge-<?= strtolower(str_replace(' ', '', $order['status'])) ?> mt-1"><?= htmlspecialchars($order['status']) ?></span>
    </div>
    <a href="/admin/orders" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Back to Orders</a>
</div>

<div class="row g-4">
    <div class="col-md-7">
        <!-- Order & Payment Review Card -->
        <div class="card-custom p-4 mb-4">
            <h5 class="text-indigo mb-3"><i class="fa-solid fa-credit-card me-2"></i>Payment Verification & Details</h5>
            <?php if ($payment): ?>
                <table class="table table-dark border-secondary">
                    <tr><th>Payment Method</th><td><strong><?= htmlspecialchars($payment['payment_method_name']) ?></strong></td></tr>
                    <tr><th>Transaction ID / TxHash</th><td><span class="text-info font-monospace fw-bold fs-5"><?= htmlspecialchars($payment['transaction_id']) ?></span></td></tr>
                    <tr><th>Amount Paid</th><td>$<?= number_format($payment['amount'], 2) ?></td></tr>
                    <tr><th>Submission Time</th><td><?= date('Y-m-d H:i:s', strtotime($payment['created_at'])) ?></td></tr>
                    <?php if ($payment['payment_note']): ?>
                        <tr><th>Customer Note</th><td><?= htmlspecialchars($payment['payment_note']) ?></td></tr>
                    <?php endif; ?>
                    <?php if ($payment['screenshot_path']): ?>
                        <tr>
                            <th>Payment Screenshot</th>
                            <td>
                                <a href="/admin/payments/screenshot/<?= $payment['id'] ?>" target="_blank" class="btn btn-outline-info btn-sm"><i class="fa-solid fa-image me-1"></i> View Screenshot Image</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </table>
            <?php else: ?>
                <div class="alert alert-warning mb-0"><i class="fa-solid fa-triangle-exclamation me-1"></i> Payment reference not yet submitted by customer.</div>
            <?php endif; ?>
        </div>

        <!-- Server Assignment & Provisioning Action Form -->
        <?php if (!in_array($order['status'], ['Active', 'Approved'])): ?>
            <div class="card-custom p-4 border-indigo">
                <h5 class="text-white mb-3"><i class="fa-solid fa-server text-indigo me-2"></i>Provision & Approve Server Order</h5>

                <form method="POST" action="/admin/orders/<?= $order['id'] ?>/approve">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label text-white">Select Server Assignment Mode</label>
                        <select name="server_mode" id="server_mode" class="form-select bg-dark text-white border-secondary" onchange="toggleServerMode(this.value)">
                            <option value="existing" selected>Assign Available Server from Inventory Pool</option>
                            <option value="new">Provision & Add New Server Node</option>
                        </select>
                    </div>

                    <!-- Existing Server Selector -->
                    <div id="existing_server_box" class="mb-3">
                        <label class="form-label text-white">Select Server from Inventory Pool</label>
                        <select name="server_id" class="form-select bg-dark text-white border-secondary">
                            <?php if (empty($availableServers)): ?>
                                <option value="">No Available Servers in Pool — Switch to "Provision New"</option>
                            <?php else: ?>
                                <?php foreach ($availableServers as $as): ?>
                                    <option value="<?= $as['id'] ?>"><?= htmlspecialchars($as['host_ip']) ?> (<?= htmlspecialchars($as['location']) ?> - <?= htmlspecialchars($as['server_type']) ?>)</option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- New Server Provisioning Fields -->
                    <div id="new_server_box" class="d-none border border-secondary p-3 rounded mb-3 bg-dark">
                        <h6 class="text-indigo mb-3 font-weight-bold">New Server Node Credentials</h6>
                        <div class="row mb-2">
                            <div class="col-md-8 mb-2">
                                <label class="form-label text-white small">Server Host/IP</label>
                                <input type="text" name="new_host_ip" class="form-control form-control-sm bg-dark text-white border-secondary" placeholder="192.168.1.100">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label text-white small">SSH Port</label>
                                <input type="number" name="new_ssh_port" class="form-control form-control-sm bg-dark text-white border-secondary" value="22">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6 mb-2">
                                <label class="form-label text-white small">Username</label>
                                <input type="text" name="new_username" class="form-control form-control-sm bg-dark text-white border-secondary" value="root">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label text-white small">Password</label>
                                <input type="password" name="new_password" class="form-control form-control-sm bg-dark text-white border-secondary">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label text-white small">Location / Region</label>
                            <input type="text" name="new_location" class="form-control form-control-sm bg-dark text-white border-secondary" value="USA - Ashburn">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-white">Admin Notes / Activation Remarks</label>
                        <textarea name="admin_notes" class="form-control bg-dark text-white border-secondary" rows="2" placeholder="e.g. Payment verified via bKash. Server provisioned on Node-04."></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success flex-grow-1 py-2"><i class="fa-solid fa-check-double me-1"></i> Approve Order & Dispatch Credentials Email</button>
                    </div>
                </form>

                <hr class="border-secondary my-4">

                <form method="POST" action="/admin/orders/<?= $order['id'] ?>/reject">
                    <?= csrf_field() ?>
                    <div class="mb-2">
                        <label class="form-label text-white">Rejection Reason</label>
                        <input type="text" name="rejection_reason" class="form-control bg-dark text-white border-secondary" placeholder="Invalid transaction reference or unverified payment..." required>
                    </div>
                    <button type="submit" class="btn btn-outline-danger btn-sm mt-2"><i class="fa-solid fa-xmark me-1"></i> Reject Order</button>
                </form>
            </div>
        <?php else: ?>
            <div class="alert alert-success"><i class="fa-solid fa-circle-check me-2"></i> This order has been approved and activated. Server credentials were sent via automated email.</div>
        <?php endif; ?>
    </div>

    <!-- Customer & Package Info Card -->
    <div class="col-md-5">
        <div class="card-custom p-4 mb-4">
            <h5 class="text-indigo mb-3"><i class="fa-solid fa-user me-2"></i>Customer Information</h5>
            <p class="text-white mb-1"><strong><?= htmlspecialchars($order['customer_name']) ?></strong></p>
            <p class="text-muted small mb-1"><i class="fa-solid fa-envelope me-1"></i> <?= htmlspecialchars($order['customer_email']) ?></p>
            <p class="text-muted small mb-1"><i class="fa-solid fa-phone me-1"></i> <?= htmlspecialchars($order['customer_phone']) ?></p>
            <p class="text-muted small mb-0"><i class="fa-solid fa-location-dot me-1"></i> <?= htmlspecialchars($order['customer_address']) ?></p>
        </div>

        <div class="card-custom p-4">
            <h5 class="text-indigo mb-3"><i class="fa-solid fa-box-open me-2"></i>Ordered Package Details</h5>
            <p class="text-white mb-1">Package: <strong><?= htmlspecialchars($order['package_name']) ?></strong></p>
            <p class="text-muted small mb-1">Daily POP Limit: <strong><?= number_format($order['daily_pop']) ?> POP/day</strong></p>
            <p class="text-muted small mb-1">Monthly POP Quota: <strong><?= number_format($order['monthly_pop']) ?> POP/month</strong></p>
            <p class="text-indigo fw-bold fs-5 mb-0">$<?= number_format($order['price'], 2) ?>/mo</p>
        </div>
    </div>
</div>

<script>
function toggleServerMode(mode) {
    if (mode === 'new') {
        document.getElementById('existing_server_box').classList.add('d-none');
        document.getElementById('new_server_box').classList.remove('d-none');
    } else {
        document.getElementById('existing_server_box').classList.remove('d-none');
        document.getElementById('new_server_box').classList.add('d-none');
    }
}
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/admin.php';
?>
