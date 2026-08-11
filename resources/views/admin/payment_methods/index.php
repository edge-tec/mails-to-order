<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-white mb-0"><i class="fa-solid fa-credit-card text-indigo me-2"></i>Payment Method Settings</h3>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPaymentModal"><i class="fa-solid fa-plus me-1"></i> Add Payment Method</button>
</div>

<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Method Name</th>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Account / Wallet Address</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($methods as $m): ?>
                    <tr>
                        <td class="fw-bold text-white"><?= htmlspecialchars($m['name']) ?></td>
                        <td class="font-monospace text-indigo"><?= htmlspecialchars($m['code']) ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($m['type']) ?></span></td>
                        <td class="font-monospace small">
                            <?php if ($m['type'] === 'mobile_wallet'): ?>
                                Number: <?= htmlspecialchars($m['personal_number']) ?>
                            <?php else: ?>
                                <?= htmlspecialchars($m['currency']) ?> (<?= htmlspecialchars($m['network']) ?>):<br><span class="text-info"><?= htmlspecialchars($m['wallet_address']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge bg-<?= $m['status'] === 'active' ? 'success' : 'danger' ?>"><?= htmlspecialchars($m['status']) ?></span></td>
                        <td>
                            <button class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#editMethodModal_<?= $m['id'] ?>"><i class="fa-solid fa-pen"></i></button>
                            <form method="POST" action="/admin/payment-methods/<?= $m['id'] ?>/delete" class="d-inline" onsubmit="return confirm('Delete method?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Method Modal -->
                    <div class="modal fade" id="editMethodModal_<?= $m['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content bg-dark text-white border-secondary">
                                <div class="modal-header border-secondary">
                                    <h5 class="modal-title">Edit Payment Method — <?= htmlspecialchars($m['name']) ?></h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" action="/admin/payment-methods/save">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                    <input type="hidden" name="code" value="<?= $m['code'] ?>">
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Method Name</label>
                                            <input type="text" name="name" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($m['name']) ?>" required>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Type</label>
                                                <select name="type" class="form-select bg-dark text-white border-secondary">
                                                    <option value="mobile_wallet" <?= $m['type'] === 'mobile_wallet' ? 'selected' : '' ?>>Mobile Wallet (bKash/Nagad)</option>
                                                    <option value="crypto" <?= $m['type'] === 'crypto' ? 'selected' : '' ?>>Cryptocurrency (USDT/BTC/ETH)</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Personal Number (Mobile Wallet)</label>
                                                <input type="text" name="personal_number" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($m['personal_number'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Crypto Currency</label>
                                                <input type="text" name="currency" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($m['currency'] ?? '') ?>" placeholder="USDT">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Network</label>
                                                <input type="text" name="network" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($m['network'] ?? '') ?>" placeholder="TRC20">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Status</label>
                                                <select name="status" class="form-select bg-dark text-white border-secondary">
                                                    <option value="active" <?= $m['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                                    <option value="disabled" <?= $m['status'] === 'disabled' ? 'selected' : '' ?>>Disabled</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Wallet Address</label>
                                            <input type="text" name="wallet_address" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($m['wallet_address'] ?? '') ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Instructions displayed to customer</label>
                                            <textarea name="instructions" class="form-control bg-dark text-white border-secondary" rows="3"><?= htmlspecialchars($m['instructions']) ?></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-secondary">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save Method</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">Add New Payment Method</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/admin/payment-methods/save">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Unique Code (Key)</label>
                            <input type="text" name="code" class="form-control bg-dark text-white border-secondary" placeholder="crypto_eth" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Method Display Name</label>
                            <input type="text" name="name" class="form-control bg-dark text-white border-secondary" placeholder="Ethereum (ETH)" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select bg-dark text-white border-secondary">
                                <option value="crypto">Cryptocurrency (USDT/BTC/ETH)</option>
                                <option value="mobile_wallet">Mobile Wallet (bKash/Nagad)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Personal Number (If Mobile Wallet)</label>
                            <input type="text" name="personal_number" class="form-control bg-dark text-white border-secondary">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Crypto Currency Symbol</label>
                            <input type="text" name="currency" class="form-control bg-dark text-white border-secondary" placeholder="USDT / BTC / ETH">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Network</label>
                            <input type="text" name="network" class="form-control bg-dark text-white border-secondary" placeholder="ERC20 / TRC20 / Mainnet">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Wallet Address</label>
                        <input type="text" name="wallet_address" class="form-control bg-dark text-white border-secondary" placeholder="0x...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Customer Instructions</label>
                        <textarea name="instructions" class="form-control bg-dark text-white border-secondary" rows="3" placeholder="1. Send payment to address below..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Payment Method</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>
