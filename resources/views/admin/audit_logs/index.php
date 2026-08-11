<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-white mb-0"><i class="fa-solid fa-clock-rotate-left text-indigo me-2"></i>System Security & Audit Logs</h3>
</div>

<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>User / Actor</th>
                    <th>Action</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr><td colspan="4" class="text-center text-muted py-4">No audit logs recorded yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($logs as $l): ?>
                        <tr>
                            <td class="small"><?= date('Y-m-d H:i:s', strtotime($l['created_at'])) ?></td>
                            <td><?= htmlspecialchars($l['user_name'] ?? 'System') ?></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($l['action']) ?></span></td>
                            <td class="font-monospace text-info"><?= htmlspecialchars($l['ip_address']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/admin.php';
?>
