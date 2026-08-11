<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-white mb-0"><i class="fa-solid fa-wallet text-indigo me-2"></i>Payment Gateways & Methods</h3>
</div>

<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Method Name</th>
                    <th>Code</th>
                    <th>Account / Number / Wallet Address</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($methods as $m): ?>
                    <tr>
                        <td class="fw-bold text-white"><?= htmlspecialchars($m['name']) ?></td>
                        <td><code><?= htmlspecialchars($m['code']) ?></code></td>
                        <td class="font-monospace text-info"><?= htmlspecialchars($m['account_details'] ?? 'N/A') ?></td>
                        <td><span class="badge bg-<?= $m['status'] === 'active' ? 'success' : 'danger' ?>"><?= htmlspecialchars($m['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/admin.php';
?>
