<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-white mb-0"><i class="fa-solid fa-receipt text-indigo me-2"></i>My Server Orders</h3>
    <a href="/packages" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> New Order</a>
</div>

<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Package Name</th>
                    <th>Daily POP</th>
                    <th>Monthly POP</th>
                    <th>Price</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No order history found.</td></tr>
                <?php else: ?>
                    <?php foreach ($orders as $o): ?>
                        <tr>
                            <td class="font-monospace text-indigo fw-bold"><?= htmlspecialchars($o['order_number']) ?></td>
                            <td><?= htmlspecialchars($o['package_name']) ?></td>
                            <td><?= number_format($o['daily_pop']) ?></td>
                            <td><?= number_format($o['monthly_pop']) ?></td>
                            <td>$<?= number_format($o['price'], 2) ?></td>
                            <td><?= date('Y-m-d H:i', strtotime($o['created_at'])) ?></td>
                            <td>
                                <span class="badge badge-status badge-<?= strtolower(str_replace(' ', '', $o['status'])) ?>"><?= htmlspecialchars($o['status']) ?></span>
                            </td>
                            <td><a href="/orders/<?= $o['id'] ?>" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-eye me-1"></i> Details</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/user.php';
?>
