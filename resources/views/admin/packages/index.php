<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-white mb-0"><i class="fa-solid fa-box-open text-indigo me-2"></i>Server Package Management</h3>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPackageModal"><i class="fa-solid fa-plus me-1"></i> Add Package</button>
</div>

<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Sort</th>
                    <th>Package Name</th>
                    <th>Type</th>
                    <th>Daily POP Limit</th>
                    <th>Monthly POP Limit</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($packages as $pkg): ?>
                    <tr>
                        <td><?= $pkg['sort_order'] ?></td>
                        <td class="fw-bold text-white"><?= htmlspecialchars($pkg['name']) ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($pkg['type']) ?></span></td>
                        <td><?= number_format($pkg['daily_pop_limit']) ?></td>
                        <td><?= number_format($pkg['monthly_pop_limit']) ?></td>
                        <td class="text-indigo fw-bold">$<?= number_format($pkg['price'], 2) ?></td>
                        <td><span class="badge bg-<?= $pkg['status'] === 'active' ? 'success' : 'danger' ?>"><?= htmlspecialchars($pkg['status']) ?></span></td>
                        <td>
                            <button class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#editPkgModal_<?= $pkg['id'] ?>"><i class="fa-solid fa-pen"></i></button>
                            <form method="POST" action="/admin/packages/<?= $pkg['id'] ?>/delete" class="d-inline" onsubmit="return confirm('Delete package?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editPkgModal_<?= $pkg['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content bg-dark text-white border-secondary">
                                <div class="modal-header border-secondary">
                                    <h5 class="modal-title">Edit Package — <?= htmlspecialchars($pkg['name']) ?></h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" action="/admin/packages/<?= $pkg['id'] ?>/edit">
                                    <?= csrf_field() ?>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Package Name</label>
                                            <input type="text" name="name" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($pkg['name']) ?>" required>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <label class="form-label">Daily POP Limit</label>
                                                <input type="number" name="daily_pop_limit" class="form-control bg-dark text-white border-secondary" value="<?= $pkg['daily_pop_limit'] ?>" required>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label">Monthly POP Limit</label>
                                                <input type="number" name="monthly_pop_limit" class="form-control bg-dark text-white border-secondary" value="<?= $pkg['monthly_pop_limit'] ?>" required>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <label class="form-label">Price ($)</label>
                                                <input type="number" step="0.01" name="price" class="form-control bg-dark text-white border-secondary" value="<?= $pkg['price'] ?>" required>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label">Status</label>
                                                <select name="status" class="form-select bg-dark text-white border-secondary">
                                                    <option value="active" <?= $pkg['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                                    <option value="disabled" <?= $pkg['status'] === 'disabled' ? 'selected' : '' ?>>Disabled</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea name="description" class="form-control bg-dark text-white border-secondary" rows="3"><?= htmlspecialchars($pkg['description']) ?></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-secondary">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
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
<div class="modal fade" id="addPackageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">Add New Server Package</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/admin/packages/create">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Package Name</label>
                        <input type="text" name="name" class="form-control bg-dark text-white border-secondary" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Daily POP Limit</label>
                            <input type="number" name="daily_pop_limit" class="form-control bg-dark text-white border-secondary" value="500" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Monthly POP Limit</label>
                            <input type="number" name="monthly_pop_limit" class="form-control bg-dark text-white border-secondary" value="7000" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Price ($)</label>
                            <input type="number" step="0.01" name="price" class="form-control bg-dark text-white border-secondary" value="29.99" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select bg-dark text-white border-secondary">
                                <option value="standard">Standard</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control bg-dark text-white border-secondary" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Package</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>
