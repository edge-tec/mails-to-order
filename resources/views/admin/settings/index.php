<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-white mb-0"><i class="fa-solid fa-gears text-indigo me-2"></i>System Configuration Settings</h3>
</div>

<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Setting Group</th>
                    <th>Key</th>
                    <th>Current Value</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($settings as $s): ?>
                    <tr>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($s['setting_group']) ?></span></td>
                        <td><code><?= htmlspecialchars($s['setting_key']) ?></code></td>
                        <td class="font-monospace text-info"><?= htmlspecialchars($s['setting_value']) ?></td>
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
