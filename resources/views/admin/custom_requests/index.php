<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-white mb-0"><i class="fa-solid fa-sliders text-indigo me-2"></i>Custom Package Requests</h3>
</div>

<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Email / Contact</th>
                    <th>Daily POP</th>
                    <th>Monthly POP</th>
                    <th>Location</th>
                    <th>Quote Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($requests)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No custom package inquiries received.</td></tr>
                <?php else: ?>
                    <?php foreach ($requests as $r): ?>
                        <tr>
                            <td class="fw-bold text-white"><?= htmlspecialchars($r['user_name']) ?></td>
                            <td class="small"><?= htmlspecialchars($r['contact_info']) ?></td>
                            <td><strong><?= number_format($r['required_daily_pop']) ?></strong> POP</td>
                            <td><strong><?= number_format($r['required_monthly_pop']) ?></strong> POP</td>
                            <td><?= htmlspecialchars($r['preferred_location']) ?></td>
                            <td class="text-indigo fw-bold"><?= $r['admin_quote_price'] ? '$' . number_format($r['admin_quote_price'], 2) : 'Not Quoted' ?></td>
                            <td><span class="badge bg-<?= $r['status'] === 'converted' ? 'success' : ($r['status'] === 'quoted' ? 'info' : 'warning') ?>"><?= htmlspecialchars($r['status']) ?></span></td>
                            <td>
                                <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#quoteModal_<?= $r['id'] ?>"><i class="fa-solid fa-calculator"></i> Quote</button>
                                <?php if ($r['admin_quote_price'] && $r['status'] !== 'converted'): ?>
                                    <form method="POST" action="/admin/custom-packages/<?= $r['id'] ?>/convert" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-success btn-sm"><i class="fa-solid fa-cart-plus me-1"></i> Convert Order</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <!-- Quote Modal -->
                        <div class="modal fade" id="quoteModal_<?= $r['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content bg-dark text-white border-secondary">
                                    <div class="modal-header border-secondary">
                                        <h5 class="modal-title">Issue Custom Quote — Request #<?= $r['id'] ?></h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="/admin/custom-packages/<?= $r['id'] ?>/quote">
                                        <?= csrf_field() ?>
                                        <div class="modal-body">
                                            <p class="text-muted small">Requirements: <?= htmlspecialchars($r['additional_requirements'] ?: 'None specified') ?></p>
                                            <div class="mb-3">
                                                <label class="form-label">Set Custom Package Price ($/mo)</label>
                                                <input type="number" step="0.01" name="admin_quote_price" class="form-control bg-dark text-white border-secondary" value="<?= $r['admin_quote_price'] ?? '' ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Admin Notes</label>
                                                <textarea name="admin_notes" class="form-control bg-dark text-white border-secondary" rows="3"><?= htmlspecialchars($r['admin_notes'] ?? '') ?></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-secondary">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Quote</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
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
