<?php
ob_start();
?>
<div class="row justify-content-center py-4">
    <div class="col-md-9">
        <div class="card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary">
                <h4 class="text-white mb-0"><i class="fa-solid fa-wallet text-indigo me-2"></i>Step 2 — Payment Method & Submission</h4>
                <span class="badge bg-primary">Order Progress 2 / 3</span>
            </div>

            <form method="POST" action="/order/step2" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <h5 class="text-white mb-3">1. Select Admin-Configured Payment Method</h5>
                <div class="row g-3 mb-4">
                    <?php foreach ($paymentMethods as $index => $pm): ?>
                        <div class="col-md-6">
                            <div class="card bg-dark border-secondary p-3 h-100">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method_code" id="pm_<?= $pm['code'] ?>" value="<?= $pm['code'] ?>" <?= $index === 0 ? 'checked' : '' ?> required>
                                    <label class="form-check-label fw-bold text-white ms-2" for="pm_<?= $pm['code'] ?>">
                                        <?= htmlspecialchars($pm['name']) ?>
                                    </label>
                                </div>
                                <div class="mt-2 text-muted small ms-4">
                                    <?php if ($pm['type'] === 'mobile_wallet'): ?>
                                        <i class="fa-solid fa-phone text-indigo me-1"></i> Personal Number: <strong class="text-light"><?= htmlspecialchars($pm['personal_number']) ?></strong>
                                    <?php elseif ($pm['type'] === 'crypto'): ?>
                                        <i class="fa-solid fa-coins text-warning me-1"></i> <?= htmlspecialchars($pm['currency']) ?> (Network: <?= htmlspecialchars($pm['network']) ?>)<br>
                                        <span class="font-monospace text-info small">Wallet: <?= htmlspecialchars($pm['wallet_address']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Payment Instructions -->
                <div class="mb-4">
                    <h5 class="text-white mb-3">2. Payment Instructions</h5>
                    <?php foreach ($paymentMethods as $index => $pm): ?>
                        <div id="instructions-<?= $pm['code'] ?>" class="payment-instructions <?= $index === 0 ? '' : 'd-none' ?> bg-dark border border-secondary p-3 rounded">
                            <h6 class="text-indigo mb-2"><i class="fa-solid fa-circle-info me-1"></i> Instructions for <?= htmlspecialchars($pm['name']) ?></h6>
                            <pre class="text-light mb-0 font-monospace small" style="white-space: pre-wrap;"><?= htmlspecialchars($pm['instructions']) ?></pre>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Payment Submission Form -->
                <h5 class="text-white mb-3">3. Enter Transaction & Reference Details</h5>
                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label text-white">Transaction ID / TxHash / Reference <span class="text-danger">*</span></label>
                        <input type="text" name="transaction_id" class="form-control bg-dark text-white border-secondary" placeholder="e.g. 9J47X8Y2 or TxHash" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white">Amount Paid ($) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($package['price']) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Upload Payment Screenshot / Receipt (Optional)</label>
                    <input type="file" name="screenshot" class="form-control bg-dark text-white border-secondary" accept="image/jpeg,image/png,image/webp">
                    <span class="text-muted small">Allowed formats: JPG, JPEG, PNG, WEBP (Max 5MB)</span>
                </div>

                <div class="mb-4">
                    <label class="form-label text-white">Payment Note / Sender Account (Optional)</label>
                    <textarea name="payment_note" class="form-control bg-dark text-white border-secondary" rows="2" placeholder="e.g. Paid from bKash number 017XXXXXXX or binance account"></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="/order/step1" class="btn btn-outline-secondary">Back to Step 1</a>
                    <button type="submit" class="btn btn-primary px-4">Review Order <i class="fa-solid fa-arrow-right ms-2"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
