<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-white mb-0"><i class="fa-solid fa-server text-indigo me-2"></i>Server Inventory & Infrastructure</h3>
    <a href="/admin/servers/create" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Add New Server Node</a>
</div>

<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Host / IP</th>
                    <th>SSH Port</th>
                    <th>Username</th>
                    <th>Type</th>
                    <th>Location</th>
                    <th>Daily POP</th>
                    <th>Status</th>
                    <th>Assignment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($servers)): ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">No servers added to inventory yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($servers as $s): ?>
                        <tr>
                            <td class="font-monospace text-indigo fw-bold"><?= htmlspecialchars($s['host_ip']) ?></td>
                            <td><?= htmlspecialchars($s['ssh_port']) ?></td>
                            <td><?= htmlspecialchars($s['username']) ?></td>
                            <td><?= htmlspecialchars($s['server_type']) ?></td>
                            <td><?= htmlspecialchars($s['location']) ?></td>
                            <td><?= number_format($s['daily_pop_limit']) ?></td>
                            <td><span class="badge bg-<?= $s['status'] === 'active' ? 'success' : 'danger' ?>"><?= htmlspecialchars($s['status']) ?></span></td>
                            <td><span class="badge badge-status badge-<?= strtolower($s['assignment_status']) ?>"><?= htmlspecialchars($s['assignment_status']) ?></span></td>
                            <td>
                                <a href="/admin/servers/<?= $s['id'] ?>/edit" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
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
