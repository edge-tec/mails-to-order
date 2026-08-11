<?php
ob_start();
$isEdit = !empty($server);
$actionUrl = $isEdit ? "/admin/servers/{$server['id']}/edit" : "/admin/servers/create";
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-white mb-0"><?= $isEdit ? "Edit Server — {$server['host_ip']}" : "Add New Server Node" ?></h3>
    <a href="/admin/servers" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Back to Inventory</a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card-custom p-4">
            <form method="POST" action="<?= $actionUrl ?>">
                <?= csrf_field() ?>
                
                <div class="row mb-3">
                    <div class="col-md-8 mb-3 mb-md-0">
                        <label class="form-label text-white">Server Host / IP Address <span class="text-danger">*</span></label>
                        <input type="text" name="host_ip" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($server['host_ip'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-white">SSH Port</label>
                        <input type="number" name="ssh_port" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($server['ssh_port'] ?? 22) ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label text-white">SSH Username</label>
                        <input type="text" name="username" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($server['username'] ?? 'root') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white">Password <?= $isEdit ? '(Leave blank to keep unchanged)' : '<span class="text-danger">*</span>' ?></label>
                        <input type="password" name="password" class="form-control bg-dark text-white border-secondary" <?= $isEdit ? '' : 'required' ?>>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label text-white">Location / Datacenter</label>
                        <input type="text" name="location" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($server['location'] ?? 'USA - Ashburn') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white">Server Type</label>
                        <input type="text" name="server_type" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($server['server_type'] ?? 'VPS Dedicated') ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label text-white">Provider Name</label>
                        <input type="text" name="provider" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($server['provider'] ?? 'Internal Cloud') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white">Initial Status</label>
                        <select name="status" class="form-select bg-dark text-white border-secondary">
                            <option value="Available" <?= ($server['status'] ?? '') === 'Available' ? 'selected' : '' ?>>Available (Pool)</option>
                            <option value="Assigned" <?= ($server['status'] ?? '') === 'Assigned' ? 'selected' : '' ?>>Assigned</option>
                            <option value="Active" <?= ($server['status'] ?? '') === 'Active' ? 'selected' : '' ?>>Active</option>
                            <option value="Suspended" <?= ($server['status'] ?? '') === 'Suspended' ? 'selected' : '' ?>>Suspended</option>
                            <option value="Maintenance" <?= ($server['status'] ?? '') === 'Maintenance' ? 'selected' : '' ?>>Maintenance</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-white">Internal Admin Notes</label>
                    <textarea name="notes" class="form-control bg-dark text-white border-secondary" rows="3"><?= htmlspecialchars($server['notes'] ?? '') ?></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="/admin/servers" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4"><?= $isEdit ? "Update Server Node" : "Add Server to Pool" ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>
