<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-white fw-bold mb-0"><i class="fa-solid fa-network-wired text-indigo me-2"></i>My Provisioned Servers</h3>
</div>

<div class="row g-4">
    <?php if (empty($servers)): ?>
        <div class="col-12">
            <div class="card-custom p-5 text-center">
                <i class="fa-solid fa-server text-light-silver fa-4x mb-3"></i>
                <h4 class="text-white fw-bold mb-2">No Servers Provisioned Yet</h4>
                <p class="text-light-silver fs-6 mb-4">Once your server order payment is verified by administration, your server details will appear here.</p>
                <div><a href="/packages" class="btn btn-primary px-4"><i class="fa-solid fa-plus me-1"></i> Order a Server Package</a></div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($servers as $s): ?>
            <div class="col-md-6">
                <div class="card-custom p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="text-white mb-0 font-monospace fw-bold"><i class="fa-solid fa-server text-indigo me-2"></i><?= htmlspecialchars($s['host_ip']) ?></h5>
                        <span class="badge badge-status badge-<?= strtolower($s['assignment_status']) ?>"><?= htmlspecialchars($s['assignment_status']) ?></span>
                    </div>

                    <div class="mb-3 text-light-silver small">
                        <p class="mb-1 text-light-silver">Package: <strong class="text-white"><?= htmlspecialchars($s['package_name']) ?></strong></p>
                        <p class="mb-1 text-light-silver">Location: <strong class="text-white"><?= htmlspecialchars($s['location']) ?></strong></p>
                        <p class="mb-1 text-light-silver">Daily Limit: <strong class="text-white"><?= number_format($s['daily_pop']) ?> POP</strong></p>
                        <p class="mb-0 text-light-silver">Expires On: <strong class="text-warning"><?= date('M d, Y', strtotime($s['expiration_date'])) ?></strong></p>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <a href="/servers/<?= $s['id'] ?>" class="btn btn-outline-primary btn-sm flex-grow-1"><i class="fa-solid fa-key me-1"></i> Access Credentials & Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/user.php';
?>
