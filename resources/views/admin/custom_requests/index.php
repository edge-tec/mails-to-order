<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-white mb-0"><i class="fa-solid fa-sliders text-indigo me-2"></i>Custom Package Requests</h3>
</div>

<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Daily POP</th>
                    <th>Monthly POP</th>
                    <th>Location</th>
                    <th>Notes</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($requests)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No custom package requests submitted.</td></tr>
                <?php else: ?>
                    <?php foreach ($requests as $r): ?>
                        <tr>
                            <td class="fw-bold text-white"><?= htmlspecialchars($r['name']) ?></td>
                            <td class="small"><?= htmlspecialchars($r['email']) ?></td>
                            <td><?= number_format($r['daily_pop']) ?> POP</td>
                            <td><?= number_format($r['monthly_pop']) ?> POP</td>
                            <td><?= htmlspecialchars($r['location_preference']) ?></td>
                            <td class="small"><?= htmlspecialchars($r['additional_notes']) ?></td>
                            <td><span class="badge bg-<?= $r['status'] === 'pending' ? 'warning' : 'success' ?>"><?= htmlspecialchars($r['status']) ?></span></td>
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
