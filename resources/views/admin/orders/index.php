<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-white mb-0"><i class="fa-solid fa-cart-shopping text-indigo me-2"></i>Admin Order Management</h3>
</div>

<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Package</th>
                    <th>Payment Method</th>
                    <th>Amount</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">No orders placed yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($orders as $o): ?>
                        <tr>
                            <td class="font-monospace text-indigo fw-bold"><?= htmlspecialchars($o['order_number']) ?></td>
                            <td><?= htmlspecialchars($o['customer_name']) ?></td>
                            <td class="small"><?= htmlspecialchars($o['customer_email']) ?></td>
                            <td><?= htmlspecialchars($o['package_name']) ?></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($o['payment_method_name'] ?? 'Pending') ?></span></td>
                            <td>$<?= number_format($o['price'], 2) ?></td>
                            <td class="small"><?= date('Y-m-d H:i', strtotime($o['created_at'])) ?></td>
                            <td><span class="badge badge-status badge-<?= strtolower(str_replace(' ', '', $o['status'])) ?>"><?= htmlspecialchars($o['status']) ?></span></td>
                            <td><a href="/admin/orders/<?= $o['id'] ?>" class="btn btn-outline-warning btn-sm"><i class="fa-solid fa-sliders"></i> Review</a></td>
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
