<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-white mb-0"><i class="fa-solid fa-box-open text-indigo me-2"></i>Server Package Management</h3>
</div>

<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Package Name</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Daily POP</th>
                    <th>Monthly POP</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($packages as $pkg): ?>
                    <tr>
                        <td class="fw-bold text-white"><?= htmlspecialchars($pkg['name']) ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($pkg['type']) ?></span></td>
                        <td class="text-indigo fw-bold">$<?= number_format($pkg['price'], 2) ?></td>
                        <td><?= number_format($pkg['daily_pop_limit']) ?></td>
                        <td><?= number_format($pkg['monthly_pop_limit']) ?></td>
                        <td><span class="badge bg-<?= $pkg['status'] === 'active' ? 'success' : 'danger' ?>"><?= htmlspecialchars($pkg['status']) ?></span></td>
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
