<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="text-white mb-0"><i class="fa-solid fa-wallet text-indigo me-2"></i>Payment Gateways & Wallet Methods</h3>
        <p class="text-light-silver small mb-0">Configure your bKash/Nagad personal numbers and Crypto wallet addresses for customer payments</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#paymentMethodModal" onclick="resetModal()">
        <i class="fa-solid fa-plus me-1"></i> Add Payment Method
    </button>
</div>

<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="text-white">Method Name</th>
                    <th class="text-white">Type</th>
                    <th class="text-white">Personal Number / Wallet Address</th>
                    <th class="text-white">Network / Currency</th>
                    <th class="text-white">Status</th>
                    <th class="text-white text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($methods)): ?>
                    <tr><td colspan="6" class="text-center text-light-silver py-4">No payment methods configured yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($methods as $m): ?>
                        <tr>
                            <td class="fw-bold text-white">
                                <?= htmlspecialchars($m['name']) ?>
                                <span class="d-block small text-light-silver font-monospace"><?= htmlspecialchars($m['code']) ?></span>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?= htmlspecialchars($m['type'] ?? 'mobile_wallet') ?></span>
                            </td>
                            <td>
                                <?php if (!empty($m['personal_number'])): ?>
                                    <div class="font-monospace text-info fw-bold"><i class="fa-solid fa-phone me-1"></i><?= htmlspecialchars($m['personal_number']) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($m['wallet_address'])): ?>
                                    <div class="font-monospace text-warning fw-bold text-break"><i class="fa-solid fa-wallet me-1"></i><?= htmlspecialchars($m['wallet_address']) ?></div>
                                <?php endif; ?>
                                <?php if (empty($m['personal_number']) && empty($m['wallet_address'])): ?>
                                    <span class="text-danger small"><i class="fa-solid fa-triangle-exclamation me-1"></i>Not Set — Click Edit</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="text-white small"><?= htmlspecialchars($m['currency'] ?? 'BDT') ?></span>
                                <?php if (!empty($m['network'])): ?>
                                    <span class="badge bg-dark text-info ms-1"><?= htmlspecialchars($m['network']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= $m['status'] === 'active' ? 'success' : 'danger' ?>"><?= htmlspecialchars($m['status']) ?></span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-outline-info btn-sm me-1" 
                                    onclick='editMethod(<?= json_encode($m, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                    <i class="fa-solid fa-pen-to-square me-1"></i> Edit Number / Wallet
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for Add / Edit Payment Method -->
<div class="modal fade" id="paymentMethodModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold text-white" id="modalTitle"><i class="fa-solid fa-wallet text-indigo me-2"></i>Configure Payment Method</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/admin/payment-methods/save">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="method_id" value="">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">Method Name</label>
                        <input type="text" name="name" id="method_name" class="form-control bg-secondary text-white border-secondary" placeholder="e.g. bKash Personal or USDT TRC20" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-white small fw-bold">Method Code</label>
                            <input type="text" name="code" id="method_code" class="form-control bg-secondary text-white border-secondary" placeholder="bkash / crypto_usdt" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white small fw-bold">Method Type</label>
                            <select name="type" id="method_type" class="form-select bg-secondary text-white border-secondary">
                                <option value="mobile_wallet">Mobile Wallet (bKash / Nagad / Rocket)</option>
                                <option value="crypto">Cryptocurrency (USDT / BTC / ETH)</option>
                                <option value="bank">Bank Transfer</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">Personal Mobile / Account Number</label>
                        <input type="text" name="personal_number" id="method_number" class="form-control bg-secondary text-white border-secondary" placeholder="e.g. 01700000000">
                        <span class="text-light-silver small">For bKash, Nagad, Rocket, or Bank account numbers</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">Crypto Wallet Address</label>
                        <input type="text" name="wallet_address" id="method_wallet" class="form-control bg-secondary text-white border-secondary" placeholder="e.g. T9yD14Nj9j7xXvZ3...">
                        <span class="text-light-silver small">For USDT, Bitcoin, or Ethereum wallet deposit addresses</span>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-white small fw-bold">Currency</label>
                            <input type="text" name="currency" id="method_currency" class="form-control bg-secondary text-white border-secondary" value="BDT">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white small fw-bold">Network (Optional)</label>
                            <input type="text" name="network" id="method_network" class="form-control bg-secondary text-white border-secondary" placeholder="e.g. TRC20, ERC20, BTC">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">Payment Instructions for Customer</label>
                        <textarea name="instructions" id="method_instructions" class="form-control bg-secondary text-white border-secondary" rows="2" placeholder="Send Cash Out / Send Money to this number..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">Status</label>
                        <select name="status" id="method_status" class="form-select bg-secondary text-white border-secondary">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-save me-1"></i> Save Payment Details</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetModal() {
    document.getElementById('modalTitle').innerText = 'Add Payment Method';
    document.getElementById('method_id').value = '';
    document.getElementById('method_name').value = '';
    document.getElementById('method_code').value = '';
    document.getElementById('method_type').value = 'mobile_wallet';
    document.getElementById('method_number').value = '';
    document.getElementById('method_wallet').value = '';
    document.getElementById('method_currency').value = 'BDT';
    document.getElementById('method_network').value = '';
    document.getElementById('method_instructions').value = '';
    document.getElementById('method_status').value = 'active';
}

function editMethod(method) {
    document.getElementById('modalTitle').innerText = 'Edit Payment Method — ' + method.name;
    document.getElementById('method_id').value = method.id || '';
    document.getElementById('method_name').value = method.name || '';
    document.getElementById('method_code').value = method.code || '';
    document.getElementById('method_type').value = method.type || 'mobile_wallet';
    document.getElementById('method_number').value = method.personal_number || '';
    document.getElementById('method_wallet').value = method.wallet_address || '';
    document.getElementById('method_currency').value = method.currency || 'BDT';
    document.getElementById('method_network').value = method.network || '';
    document.getElementById('method_instructions').value = method.instructions || '';
    document.getElementById('method_status').value = method.status || 'active';
    
    var modal = new bootstrap.Modal(document.getElementById('paymentMethodModal'));
    modal.show();
}
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/admin.php';
?>
