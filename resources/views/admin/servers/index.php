<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-white mb-0"><i class="fa-solid fa-server text-indigo me-2"></i>Server Inventory Pool</h3>
    <a href="/admin/servers/create" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> Add Server Node</a>
</div>

<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Host / IP</th>
                    <th>Port</th>
                    <th>Username</th>
                    <th>Encrypted Password</th>
                    <th>Location</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($servers)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No servers in pool. <a href="/admin/servers/create">Add your first server node</a></td></tr>
                <?php else: ?>
                    <?php foreach ($servers as $s): ?>
                        <tr>
                            <td class="font-monospace text-indigo fw-bold"><?= htmlspecialchars($s['host_ip']) ?></td>
                            <td><?= htmlspecialchars($s['ssh_port']) ?></td>
                            <td><?= htmlspecialchars($s['username']) ?></td>
                            <td class="font-monospace small text-muted">
                                <span id="admin-pwd-<?= $s['id'] ?>">••••••••••••</span>
                                <button class="btn btn-link btn-sm p-0 ms-1 text-info text-decoration-none" onclick="document.getElementById('admin-pwd-<?= $s['id'] ?>').innerText = '<?= htmlspecialchars($s['decrypted_password']) ?>'"><i class="fa-solid fa-eye"></i></button>
                            </td>
                            <td><?= htmlspecialchars($s['location']) ?></td>
                            <td><?= htmlspecialchars($s['server_type']) ?></td>
                            <td><span class="badge badge-status badge-<?= strtolower($s['status']) ?>"><?= htmlspecialchars($s['status']) ?></span></td>
                            <td>
                                <a href="/admin/servers/<?= $s['id'] ?>/edit" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-pen"></i></a>
                                <?php if ($s['status'] === 'Active'): ?>
                                    <form method="POST" action="/admin/servers/<?= $s['id'] ?>/status/Suspended" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-warning btn-sm" onclick="return confirm('Suspend this server?')">Suspend</button>
                                    </form>
                                <?php elseif ($s['status'] === 'Suspended'): ?>
                                    <form method="POST" action="/admin/servers/<?= $s['id'] ?>/status/Active" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-success btn-sm">Activate</button>
                                    </form>
                                <?php endif; ?>
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
require __DIR__ . '/../layouts/admin.php';
?>
