<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-white mb-0"><i class="fa-solid fa-envelope-open-text text-indigo me-2"></i>Automated Email Templates</h3>
</div>

<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Template Name</th>
                    <th>Code Key</th>
                    <th>Subject Line</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($templates as $t): ?>
                    <tr>
                        <td class="fw-bold text-white"><?= htmlspecialchars($t['name']) ?></td>
                        <td><code><?= htmlspecialchars($t['template_key']) ?></code></td>
                        <td><?= htmlspecialchars($t['subject']) ?></td>
                        <td><span class="badge bg-success">Active</span></td>
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
