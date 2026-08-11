<?php
ob_start();
?>
<div class="row justify-content-center py-4">
    <div class="col-md-8">
        <div class="card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary">
                <h4 class="text-white mb-0"><i class="fa-solid fa-user text-indigo me-2"></i>Step 1 — Customer Information</h4>
                <span class="badge bg-primary">Order Progress 1 / 3</span>
            </div>

            <div class="alert alert-info border-0 bg-primary bg-opacity-10 text-indigo mb-4">
                <strong>Selected Package:</strong> <?= htmlspecialchars($package['package_name']) ?> — $<?= number_format($package['price'], 2) ?>/mo (<?= number_format($package['daily_pop']) ?> Daily POP)
            </div>

            <form method="POST" action="/order/step1">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label text-white">Full Name</label>
                    <input type="text" name="customer_name" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label text-white">Email Address</label>
                        <input type="email" name="customer_email" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white">Phone Number</label>
                        <input type="text" name="customer_phone" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label text-white">Full Billing Address</label>
                    <textarea name="customer_address" class="form-control bg-dark text-white border-secondary" rows="3" required><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="/packages" class="btn btn-outline-secondary">Cancel Order</a>
                    <button type="submit" class="btn btn-primary px-4">Continue to Payment <i class="fa-solid fa-arrow-right ms-2"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
