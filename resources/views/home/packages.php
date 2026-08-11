<?php
ob_start();
?>
<div class="text-center py-4">
    <h1 class="text-white fw-bold">Server Packages & POP Quotas</h1>
    <p class="text-muted">Select an optimized package below to start your order process</p>
</div>

<div class="row g-4 my-3">
    <?php foreach ($packages as $pkg): ?>
        <div class="col-md-4">
            <div class="pricing-card text-center h-100 d-flex flex-column justify-content-between">
                <div>
                    <h3 class="text-white mb-2"><?= htmlspecialchars($pkg['name']) ?></h3>
                    <p class="text-muted small mb-4"><?= htmlspecialchars($pkg['description']) ?></p>
                    
                    <div class="price mb-4">
                        <?php if ($pkg['type'] === 'custom'): ?>
                            <span class="fs-4">Custom Quote</span>
                        <?php else: ?>
                            $<?= number_format($pkg['price'], 2) ?><span class="fs-6 text-muted">/mo</span>
                        <?php endif; ?>
                    </div>

                    <ul class="list-unstyled text-muted small mb-4 text-start">
                        <?php if ($pkg['type'] === 'custom'): ?>
                            <li class="mb-2"><i class="fa-solid fa-sliders text-indigo me-2"></i>Custom Daily POP Capacity</li>
                            <li class="mb-2"><i class="fa-solid fa-sliders text-indigo me-2"></i>Custom Monthly POP Quota</li>
                            <li class="mb-2"><i class="fa-solid fa-sliders text-indigo me-2"></i>Preferred Location Selection</li>
                        <?php else: ?>
                            <li class="mb-2"><i class="fa-solid fa-check text-indigo me-2"></i><strong><?= number_format($pkg['daily_pop_limit']) ?></strong> POP per day</li>
                            <li class="mb-2"><i class="fa-solid fa-check text-indigo me-2"></i>Up to <strong><?= number_format($pkg['monthly_pop_limit']) ?></strong> POP monthly</li>
                            <li class="mb-2"><i class="fa-solid fa-check text-indigo me-2"></i>Dedicated Server Host/IP</li>
                        <?php endif; ?>
                        <li class="mb-2"><i class="fa-solid fa-check text-indigo me-2"></i>POP Usage Dashboard Metrics</li>
                    </ul>
                </div>

                <div>
                    <?php if ($pkg['type'] === 'custom'): ?>
                        <a href="/custom-package-request" class="btn btn-outline-primary w-100 py-2">Request Custom Package</a>
                    <?php else: ?>
                        <a href="/order/package/<?= $pkg['id'] ?>" class="btn btn-primary w-100 py-2">Order Now</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
