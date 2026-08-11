<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="text-white fw-bold">Admin Dashboard Overview</h2>
        <p class="text-white small mb-0">System performance metrics, revenue summary, and pending actions</p>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card-custom p-3 border-start border-4 border-primary">
            <span class="text-white small text-uppercase fw-bold d-block mb-1">Total Revenue</span>
            <h3 class="text-white mb-0 mt-1">$<?= number_format($stats['total_revenue'], 2) ?></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-custom p-3 border-start border-4 border-warning">
            <span class="text-white small text-uppercase fw-bold d-block mb-1">Orders Under Review</span>
            <h3 class="text-warning mb-0 mt-1"><?= $stats['under_review'] ?></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-custom p-3 border-start border-4 border-success">
            <span class="text-white small text-uppercase fw-bold d-block mb-1">Active Provisioned Servers</span>
            <h3 class="text-success mb-0 mt-1"><?= $stats['active_servers'] ?></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-custom p-3 border-start border-4 border-info">
            <span class="text-white small text-uppercase fw-bold d-block mb-1">Registered Users</span>
            <h3 class="text-info mb-0 mt-1"><?= $stats['total_users'] ?></h3>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card-custom p-3">
            <span class="text-white small text-uppercase fw-bold d-block mb-1">Total Orders</span>
            <h4 class="text-white mb-0 mt-1"><?= $stats['total_orders'] ?></h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-custom p-3">
            <span class="text-white small text-uppercase fw-bold d-block mb-1">Pending Payment</span>
            <h4 class="text-white mb-0 mt-1"><?= $stats['pending_orders'] ?></h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-custom p-3">
            <span class="text-white small text-uppercase fw-bold d-block mb-1">Approved Orders</span>
            <h4 class="text-white mb-0 mt-1"><?= $stats['approved_orders'] ?></h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-custom p-3">
            <span class="text-white small text-uppercase fw-bold d-block mb-1">Suspended Servers</span>
            <h4 class="text-danger mb-0 mt-1"><?= $stats['suspended_servers'] ?></h4>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-md-8">
        <div class="card-custom p-4">
            <h5 class="text-white mb-3"><i class="fa-solid fa-chart-line text-indigo me-2"></i>Revenue & Order Trends</h5>
            <canvas id="revenueChart" height="220"></canvas>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom p-4">
            <h5 class="text-white mb-3"><i class="fa-solid fa-chart-pie text-indigo me-2"></i>Package Distribution</h5>
            <canvas id="packageChart" height="220"></canvas>
        </div>
    </div>
</div>

<!-- Recent Orders Table -->
<div class="card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="text-white mb-0"><i class="fa-solid fa-clock-rotate-left me-2"></i>Recent Orders Waiting Review</h5>
        <a href="/admin/orders" class="btn btn-outline-secondary btn-sm">Manage Orders</a>
    </div>

    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="text-white">Order #</th>
                    <th class="text-white">Customer Email</th>
                    <th class="text-white">Package</th>
                    <th class="text-white">Amount</th>
                    <th class="text-white">Status</th>
                    <th class="text-white">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentOrders)): ?>
                    <tr><td colspan="6" class="text-center text-white py-3">No orders found.</td></tr>
                <?php else: ?>
                    <?php foreach ($recentOrders as $o): ?>
                        <tr>
                            <td class="font-monospace text-indigo fw-bold"><?= htmlspecialchars($o['order_number']) ?></td>
                            <td class="text-white"><?= htmlspecialchars($o['customer_email']) ?></td>
                            <td class="text-white"><?= htmlspecialchars($o['package_name']) ?></td>
                            <td class="text-white">$<?= number_format($o['price'], 2) ?></td>
                            <td><span class="badge badge-status badge-<?= strtolower(str_replace(' ', '', $o['status'])) ?>"><?= htmlspecialchars($o['status']) ?></span></td>
                            <td><a href="/admin/orders/<?= $o['id'] ?>" class="btn btn-outline-warning btn-sm"><i class="fa-solid fa-sliders"></i> Review</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const ctx1 = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
            datasets: [{
                label: 'Monthly Revenue ($)',
                data: [1200, 1900, 3000, 2500, 4200, 5100, 6800, <?= $stats['total_revenue'] ?>],
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: { plugins: { legend: { labels: { color: '#ffffff' } } }, scales: { x: { ticks: { color: '#ffffff' } }, y: { ticks: { color: '#ffffff' } } } }
    });

    // Package Chart
    const ctx2 = document.getElementById('packageChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Daily 500 POP', 'Daily 1,000 POP', 'Custom Enterprise'],
            datasets: [{
                data: [45, 35, 20],
                backgroundColor: ['#6366f1', '#10b981', '#f59e0b']
            }]
        },
        options: { plugins: { legend: { labels: { color: '#ffffff' } } } }
    });
});
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>
