<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="text-white mb-0"><i class="fa-solid fa-box-open text-indigo me-2"></i>Server Package Management</h3>
        <p class="text-light-silver small mb-0">Create, edit, or configure server POP quota packages and monthly pricing</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#packageModal" onclick="resetPackageModal()">
        <i class="fa-solid fa-plus me-1"></i> Create New Package
    </button>
</div>

<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="text-white">Package Name</th>
                    <th class="text-white">Type</th>
                    <th class="text-white">Price</th>
                    <th class="text-white">Daily POP</th>
                    <th class="text-white">Monthly POP</th>
                    <th class="text-white">Sort Order</th>
                    <th class="text-white">Status</th>
                    <th class="text-white text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($packages)): ?>
                    <tr><td colspan="8" class="text-center text-light-silver py-4">No packages created yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($packages as $pkg): ?>
                        <tr>
                            <td class="fw-bold text-white">
                                <?= htmlspecialchars($pkg['name']) ?>
                                <?php if (!empty($pkg['description'])): ?>
                                    <span class="d-block small text-light-silver font-monospace"><?= htmlspecialchars($pkg['description']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($pkg['type']) ?></span></td>
                            <td class="text-indigo fw-bold">$<?= number_format($pkg['price'], 2) ?></td>
                            <td class="text-white"><?= number_format($pkg['daily_pop_limit']) ?> POP</td>
                            <td class="text-white"><?= number_format($pkg['monthly_pop_limit']) ?> POP</td>
                            <td class="text-white"><?= (int)$pkg['sort_order'] ?></td>
                            <td><span class="badge bg-<?= $pkg['status'] === 'active' ? 'success' : 'danger' ?>"><?= htmlspecialchars($pkg['status']) ?></span></td>
                            <td class="text-end">
                                <button class="btn btn-outline-info btn-sm me-1" 
                                    onclick='editPackage(<?= json_encode($pkg, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                    <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for Create / Edit Package -->
<div class="modal fade" id="packageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold text-white" id="packageModalTitle"><i class="fa-solid fa-box-open text-indigo me-2"></i>Configure Server Package</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="packageForm" method="POST" action="/admin/packages/create">
                <?= csrf_field() ?>
                
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label text-white small fw-bold">Package Name</label>
                            <input type="text" name="name" id="pkg_name" class="form-control bg-secondary text-white border-secondary" placeholder="e.g. Daily 500 POP or Enterprise 5,000 POP" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-white small fw-bold">Package Type</label>
                            <select name="type" id="pkg_type" class="form-select bg-secondary text-white border-secondary">
                                <option value="standard">Standard Package</option>
                                <option value="custom">Custom Quote Package</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label text-white small fw-bold">Monthly Price ($ USD)</label>
                            <input type="number" step="0.01" name="price" id="pkg_price" class="form-control bg-secondary text-white border-secondary" value="29.99" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-white small fw-bold">Daily POP Limit</label>
                            <input type="number" name="daily_pop_limit" id="pkg_daily" class="form-control bg-secondary text-white border-secondary" value="500" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-white small fw-bold">Monthly POP Limit</label>
                            <input type="number" name="monthly_pop_limit" id="pkg_monthly" class="form-control bg-secondary text-white border-secondary" value="7000" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">Description / Marketing Text</label>
                        <input type="text" name="description" id="pkg_description" class="form-control bg-secondary text-white border-secondary" placeholder="e.g. Ideal for small-scale email delivery and automated server tasks.">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-white small fw-bold">Display Sort Order</label>
                            <input type="number" name="sort_order" id="pkg_sort_order" class="form-control bg-secondary text-white border-secondary" value="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white small fw-bold">Status</label>
                            <select name="status" id="pkg_status" class="form-select bg-secondary text-white border-secondary">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-save me-1"></i> Save Package</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetPackageModal() {
    document.getElementById('packageModalTitle').innerText = 'Create New Server Package';
    document.getElementById('packageForm').action = '/admin/packages/create';
    document.getElementById('pkg_name').value = '';
    document.getElementById('pkg_type').value = 'standard';
    document.getElementById('pkg_price').value = '29.99';
    document.getElementById('pkg_daily').value = '500';
    document.getElementById('pkg_monthly').value = '7000';
    document.getElementById('pkg_description').value = '';
    document.getElementById('pkg_sort_order').value = '1';
    document.getElementById('pkg_status').value = 'active';
}

function editPackage(pkg) {
    document.getElementById('packageModalTitle').innerText = 'Edit Package — ' + pkg.name;
    document.getElementById('packageForm').action = '/admin/packages/' + pkg.id + '/edit';
    document.getElementById('pkg_name').value = pkg.name || '';
    document.getElementById('pkg_type').value = pkg.type || 'standard';
    document.getElementById('pkg_price').value = pkg.price || '0';
    document.getElementById('pkg_daily').value = pkg.daily_pop_limit || '0';
    document.getElementById('pkg_monthly').value = pkg.monthly_pop_limit || '0';
    document.getElementById('pkg_description').value = pkg.description || '';
    document.getElementById('pkg_sort_order').value = pkg.sort_order || '0';
    document.getElementById('pkg_status').value = pkg.status || 'active';
    
    var modal = new bootstrap.Modal(document.getElementById('packageModal'));
    modal.show();
}
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/admin.php';
?>
