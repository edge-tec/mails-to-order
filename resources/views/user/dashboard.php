<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="text-white fw-bold">Welcome back, <?= htmlspecialchars($user['name']) ?>!</h2>
        <p class="text-light-silver small mb-0">Overview of your active servers, POP quotas, and recent orders</p>
    </div>
    <a href="/packages" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Order New Server</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card-custom p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-light-silver small text-uppercase fw-bold d-block mb-1">Active Servers</span>
                    <h3 class="text-white fw-bold mb-0 mt-1"><?= $activeCount ?></h3>
                </div>
                <i class="fa-solid fa-server text-indigo fa-2x"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-custom p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-light-silver small text-uppercase fw-bold d-block mb-1">Total Orders</span>
                    <h3 class="text-white fw-bold mb-0 mt-1"><?= count($orders) ?></h3>
                </div>
                <i class="fa-solid fa-receipt text-warning fa-2x"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-custom p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-light-silver small text-uppercase fw-bold d-block mb-1">Daily POP Limit</span>
                    <h3 class="text-white fw-bold mb-0 mt-1"><?= $popMetrics ? number_format($popMetrics['daily_limit']) : '—' ?></h3>
                </div>
                <i class="fa-solid fa-bolt text-accent fa-2x"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-custom p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-light-silver small text-uppercase fw-bold d-block mb-1">Daily POP Remaining</span>
                    <h3 class="text-indigo fw-bold mb-0 mt-1"><?= $popMetrics ? number_format($popMetrics['daily_remaining']) : '—' ?></h3>
                </div>
                <i class="fa-solid fa-chart-pie text-info fa-2x"></i>
            </div>
        </div>
    </div>
</div>

<?php if ($latestServer && $popMetrics): ?>
    <div class="card-custom p-4 mb-4 border-indigo">
        <h5 class="text-white fw-bold mb-3"><i class="fa-solid fa-gauge-high text-indigo me-2"></i>Live POP Usage Tracking — Host: <?= htmlspecialchars($latestServer['host_ip']) ?></h5>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="bg-dark p-3 rounded border border-secondary">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-white fw-bold">Daily POP Usage:</span>
                        <span class="text-indigo font-monospace fw-bold"><?= number_format($popMetrics['daily_used']) ?> / <?= number_format($popMetrics['daily_limit']) ?></span>
                    </div>
                    <div class="progress bg-secondary" style="height: 10px;">
                        <?php $dPct = $popMetrics['daily_limit'] > 0 ? min(100, ($popMetrics['daily_used'] / $popMetrics['daily_limit']) * 100) : 0; ?>
                        <div class="progress-bar bg-indigo" style="width: <?= $dPct ?>%"></div>
                    </div>
                    <span class="text-light-silver small mt-2 d-block">Remaining Daily POP: <strong class="text-white"><?= number_format($popMetrics['daily_remaining']) ?></strong></span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="bg-dark p-3 rounded border border-secondary">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-white fw-bold">Monthly POP Usage:</span>
                        <span class="text-indigo font-monospace fw-bold"><?= number_format($popMetrics['monthly_used']) ?> / <?= number_format($popMetrics['monthly_limit']) ?></span>
                    </div>
                    <div class="progress bg-secondary" style="height: 10px;">
                        <?php $mPct = $popMetrics['monthly_limit'] > 0 ? min(100, ($popMetrics['monthly_used'] / $popMetrics['monthly_limit']) * 100) : 0; ?>
                        <div class="progress-bar bg-accent" style="width: <?= $mPct ?>%"></div>
                    </div>
                    <span class="text-light-silver small mt-2 d-block">Remaining Monthly POP: <strong class="text-white"><?= number_format($popMetrics['monthly_remaining']) ?></strong></span>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="text-white fw-bold mb-0"><i class="fa-solid fa-receipt me-2"></i>Recent Orders</h5>
        <a href="/orders" class="btn btn-outline-secondary btn-sm">View All Orders</a>
    </div>

    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle">
            <thead>
                <tr>
                    <th class="text-white">Order Number</th>
                    <th class="text-white">Package</th>
                    <th class="text-white">Amount</th>
                    <th class="text-white">Order Date</th>
                    <th class="text-white">Status</th>
                    <th class="text-white">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr><td colspan="6" class="text-center text-light-silver py-4">No orders placed yet. <a href="/packages" class="text-indigo fw-bold text-decoration-underline">Order your first server</a></td></tr>
                <?php else: ?>
                    <?php foreach (array_slice($orders, 0, 5) as $o): ?>
                        <tr>
                            <td class="font-monospace text-indigo fw-bold"><?= htmlspecialchars($o['order_number']) ?></td>
                            <td class="text-white"><?= htmlspecialchars($o['package_name']) ?></td>
                            <td class="text-white">$<?= number_format($o['price'], 2) ?></td>
                            <td class="text-light-silver"><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
                            <td>
                                <span class="badge badge-status badge-<?= strtolower(str_replace(' ', '', $o['status'])) ?>"><?= htmlspecialchars($o['status']) ?></span>
                            </td>
                            <td><a href="/orders/<?= $o['id'] ?>" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-eye"></i> View</a></td>
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
