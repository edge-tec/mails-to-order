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
                    <th class="text-white">Host / IP</th>
                    <th class="text-white">SSH Port</th>
                    <th class="text-white">Username</th>
                    <th class="text-white">Type</th>
                    <th class="text-white">Location</th>
                    <th class="text-white">Daily POP</th>
                    <th class="text-white">Status</th>
                    <th class="text-white">Assignment</th>
                    <th class="text-white">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($servers)): ?>
                    <tr><td colspan="9" class="text-center text-light-silver py-4">No servers added to inventory yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($servers as $s): ?>
                        <?php 
                            $dailyPop = $s['daily_pop'] ?? $s['daily_pop_limit'] ?? 0;
                            $assignStatus = $s['assignment_status'] ?? 'available';
                            $status = $s['status'] ?? 'active';
                        ?>
                        <tr>
                            <td class="font-monospace text-indigo fw-bold"><?= htmlspecialchars($s['host_ip'] ?? '') ?></td>
                            <td class="text-white"><?= htmlspecialchars((string)($s['ssh_port'] ?? 22)) ?></td>
                            <td class="text-white"><?= htmlspecialchars($s['username'] ?? 'root') ?></td>
                            <td class="text-white"><?= htmlspecialchars($s['server_type'] ?? 'VPS') ?></td>
                            <td class="text-white"><?= htmlspecialchars($s['location'] ?? 'USA') ?></td>
                            <td class="text-white fw-bold"><?= number_format((float)$dailyPop) ?> POP</td>
                            <td><span class="badge bg-<?= $status === 'active' ? 'success' : 'danger' ?>"><?= htmlspecialchars($status) ?></span></td>
                            <td><span class="badge badge-status badge-<?= strtolower($assignStatus) ?>"><?= htmlspecialchars($assignStatus) ?></span></td>
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
