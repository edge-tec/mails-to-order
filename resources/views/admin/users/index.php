<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="text-white mb-0"><i class="fa-solid fa-users text-indigo me-2"></i>Registered Customer Accounts</h3>
        <p class="text-light-silver small mb-0">View registered users, inspect order histories, and login as any user without password</p>
    </div>
</div>

<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="text-white">Customer Name</th>
                    <th class="text-white">Email Address</th>
                    <th class="text-white">Phone</th>
                    <th class="text-white">Role</th>
                    <th class="text-white">Status</th>
                    <th class="text-white">Orders</th>
                    <th class="text-white">Servers</th>
                    <th class="text-white">Registered</th>
                    <th class="text-white text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr><td colspan="9" class="text-center text-light-silver py-4">No registered users found.</td></tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td class="fw-bold text-white">
                                <?= htmlspecialchars($u['name']) ?>
                            </td>
                            <td class="small text-info font-monospace"><?= htmlspecialchars($u['email']) ?></td>
                            <td class="text-white small"><?= htmlspecialchars($u['phone'] ?? 'N/A') ?></td>
                            <td>
                                <span class="badge bg-<?= in_array($u['role'], ['admin', 'super_admin']) ? 'warning text-dark' : 'secondary' ?>">
                                    <?= htmlspecialchars($u['role']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $u['status'] === 'active' ? 'success' : 'danger' ?>"><?= htmlspecialchars($u['status']) ?></span>
                            </td>
                            <td class="text-white fw-bold"><?= (int)$u['order_count'] ?></td>
                            <td class="text-white fw-bold"><?= (int)$u['server_count'] ?></td>
                            <td class="text-light-silver small"><?= date('Y-m-d', strtotime($u['created_at'])) ?></td>
                            <td class="text-end">
                                <form method="POST" action="/admin/users/<?= $u['id'] ?>/impersonate" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-outline-warning btn-sm" onclick="return confirm('Log in as <?= htmlspecialchars($u['name']) ?> without password?')">
                                        <i class="fa-solid fa-right-to-bracket me-1"></i> Login as User
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/admin.php';
?>
