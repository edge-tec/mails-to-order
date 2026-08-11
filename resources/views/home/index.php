<?php
ob_start();
?>
<div class="row align-items-center py-5">
    <div class="col-lg-7">
        <span class="badge bg-primary bg-opacity-20 text-indigo px-3 py-2 rounded-pill mb-3 fs-6">Enterprise Cloud & POP Infrastructure</span>
        <h1 class="display-4 fw-extrabold text-white mb-3">High Performance POP Server Ordering & Provisioning</h1>
        <p class="lead text-light mb-4">Deploy optimized POP servers for automated operations, custom daily POP limits, and reliable message routing with instant credential delivery.</p>
        <div class="d-flex gap-3">
            <a href="/packages" class="btn btn-primary btn-lg px-4"><i class="fa-solid fa-rocket me-2"></i>Explore Server Packages</a>
            <a href="/contact" class="btn btn-outline-light btn-lg px-4">Contact Sales</a>
        </div>
    </div>
    <div class="col-lg-5 text-center mt-5 mt-lg-0">
        <div class="card-custom p-4 shadow-lg border-indigo">
            <div class="p-3">
                <i class="fa-solid fa-server text-indigo fa-5x mb-3"></i>
                <h4 class="text-white fw-bold">POP Capacity Engine</h4>
                <p class="text-light small mb-4">Configurable Daily 500 POP, 1,000 POP, and Enterprise subnets with AES-256-GCM encrypted credentials.</p>
                <div class="d-grid gap-2 mt-4">
                    <a href="/register" class="btn btn-primary">Create Customer Account</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-5">
    <div class="text-center mb-5">
        <h2 class="text-white fw-bold">Configured Server Packages</h2>
        <p class="text-light fs-5">Choose your daily POP quota or request custom specifications</p>
    </div>

    <div class="row g-4">
        <?php foreach ($packages as $pkg): ?>
            <div class="col-md-4">
                <div class="pricing-card <?= $pkg['sort_order'] == 2 ? 'featured' : '' ?> text-center h-100 d-flex flex-column justify-content-between">
                    <div>
                        <?php if ($pkg['sort_order'] == 2): ?>
                            <span class="badge bg-primary px-3 py-1 rounded-pill mb-3">Most Popular</span>
                        <?php endif; ?>
                        <h3 class="text-white mb-2 fw-bold"><?= htmlspecialchars($pkg['name']) ?></h3>
                        <p class="text-light small mb-4"><?= htmlspecialchars($pkg['description']) ?></p>
                        
                        <div class="price mb-4">
                            <?php if ($pkg['type'] === 'custom'): ?>
                                <span class="fs-4 text-white">Custom</span>
                            <?php else: ?>
                                $<span class="text-white"><?= number_format($pkg['price'], 2) ?></span><span class="fs-6 text-light">/mo</span>
                            <?php endif; ?>
                        </div>

                        <ul class="list-unstyled text-light small mb-4 text-start">
                            <?php if ($pkg['type'] === 'custom'): ?>
                                <li class="mb-2"><i class="fa-solid fa-check text-indigo me-2"></i>Custom Daily POP Capacity</li>
                                <li class="mb-2"><i class="fa-solid fa-check text-indigo me-2"></i>Custom Monthly POP Quota</li>
                                <li class="mb-2"><i class="fa-solid fa-check text-indigo me-2"></i>Preferred Location Selection</li>
                            <?php else: ?>
                                <li class="mb-2"><i class="fa-solid fa-check text-indigo me-2"></i><strong><?= number_format($pkg['daily_pop_limit']) ?></strong> POP Limit / day</li>
                                <li class="mb-2"><i class="fa-solid fa-check text-indigo me-2"></i>Up to <strong><?= number_format($pkg['monthly_pop_limit']) ?></strong> Monthly POP</li>
                                <li class="mb-2"><i class="fa-solid fa-check text-indigo me-2"></i>Full Root / SSH Access</li>
                            <?php endif; ?>
                            <li class="mb-2"><i class="fa-solid fa-check text-indigo me-2"></i>Instant Email Credential Dispatch</li>
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
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
