<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-white mb-0"><i class="fa-solid fa-file-shield text-indigo me-2"></i>Admin Action Audit Logs</h3>
</div>

<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Administrator</th>
                    <th>Action</th>
                    <th>Target</th>
                    <th>Details</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No audit log entries recorded.</td></tr>
                <?php else: ?>
                    <?php foreach ($logs as $l): ?>
                        <tr>
                            <td class="small text-muted font-monospace"><?= date('Y-m-d H:i:s', strtotime($l['created_at'])) ?></td>
                            <td class="fw-bold text-white"><?= htmlspecialchars($l['admin_name']) ?></td>
                            <td><span class="badge bg-indigo"><?= htmlspecialchars($l['action']) ?></span></td>
                            <td class="small"><?= htmlspecialchars($l['target_type'] ?? '—') ?> #<?= htmlspecialchars($l['target_id'] ?? '') ?></td>
                            <td class="small text-light"><?= htmlspecialchars($l['details']) ?></td>
                            <td class="font-monospace small text-info"><?= htmlspecialchars($l['ip_address']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>
