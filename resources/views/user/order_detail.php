<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-white mb-0">Order #<?= htmlspecialchars($order['order_number']) ?></h3>
    <a href="/orders" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Back to Orders</a>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card-custom p-4 mb-4">
            <h5 class="text-indigo mb-3"><i class="fa-solid fa-circle-info me-2"></i>Order Summary</h5>
            <table class="table table-dark border-secondary">
                <tr><th>Status</th><td><span class="badge badge-status badge-<?= strtolower(str_replace(' ', '', $order['status'])) ?>"><?= htmlspecialchars($order['status']) ?></span></td></tr>
                <tr><th>Package Name</th><td><?= htmlspecialchars($order['package_name']) ?></td></tr>
                <tr><th>Daily POP Limit</th><td><?= number_format($order['daily_pop']) ?> POP/day</td></tr>
                <tr><th>Monthly POP Quota</th><td><?= number_format($order['monthly_pop']) ?> POP/month</td></tr>
                <tr><th>Price</th><td>$<?= number_format($order['price'], 2) ?></td></tr>
                <tr><th>Order Date</th><td><?= date('F d, Y - H:i', strtotime($order['created_at'])) ?></td></tr>
                <?php if ($order['admin_notes']): ?>
                    <tr><th>Admin Notes</th><td class="text-warning"><?= htmlspecialchars($order['admin_notes']) ?></td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-custom p-4">
            <h5 class="text-indigo mb-3"><i class="fa-solid fa-user me-2"></i>Customer Information</h5>
            <p class="text-white mb-1"><strong><?= htmlspecialchars($order['customer_name']) ?></strong></p>
            <p class="text-muted small mb-1"><?= htmlspecialchars($order['customer_email']) ?></p>
            <p class="text-muted small mb-1"><?= htmlspecialchars($order['customer_phone']) ?></p>
            <p class="text-muted small mb-0"><?= htmlspecialchars($order['customer_address']) ?></p>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/user.php';
?>
