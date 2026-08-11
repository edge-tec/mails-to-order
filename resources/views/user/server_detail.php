<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="text-white mb-0">Server Credentials — <?= htmlspecialchars($server['host_ip']) ?></h3>
        <span class="badge badge-status badge-<?= strtolower($server['assignment_status']) ?> mt-1"><?= htmlspecialchars($server['assignment_status']) ?></span>
    </div>
    <a href="/servers" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Back to Servers</a>
</div>

<div class="row g-4 mb-4">
    <!-- Server Login Credentials Box -->
    <div class="col-md-7">
        <div class="card-custom p-4 border-indigo">
            <h5 class="text-white mb-3"><i class="fa-solid fa-lock text-indigo me-2"></i>Secure Server Access Credentials</h5>
            
            <div class="mb-3">
                <label class="text-muted small">Server IP / Host</label>
                <div class="secret-box">
                    <span id="copy-ip"><?= htmlspecialchars($server['host_ip']) ?></span>
                    <button class="btn btn-dark btn-sm text-indigo" onclick="copyToClipboard('<?= htmlspecialchars($server['host_ip']) ?>', this)"><i class="fa-solid fa-copy"></i> Copy</button>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label class="text-muted small">SSH Username</label>
                    <div class="secret-box">
                        <span id="copy-user"><?= htmlspecialchars($server['username']) ?></span>
                        <button class="btn btn-dark btn-sm text-indigo" onclick="copyToClipboard('<?= htmlspecialchars($server['username']) ?>', this)"><i class="fa-solid fa-copy"></i> Copy</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small">SSH Port</label>
                    <div class="secret-box">
                        <span id="copy-port"><?= htmlspecialchars($server['ssh_port']) ?></span>
                        <button class="btn btn-dark btn-sm text-indigo" onclick="copyToClipboard('<?= htmlspecialchars($server['ssh_port']) ?>', this)"><i class="fa-solid fa-copy"></i> Copy</button>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="text-muted small">Server Root Password</label>
                <div class="secret-box">
                    <span id="pwd-placeholder" data-revealed="false">••••••••••••</span>
                    <div>
                        <button class="btn btn-outline-info btn-sm me-1" onclick="revealServerPassword(<?= $server['id'] ?>, '<?= csrf_token() ?>', 'pwd-placeholder')"><i class="fa-solid fa-eye me-1"></i> Show Password</button>
                        <button class="btn btn-dark btn-sm text-indigo" onclick="copyToClipboard(document.getElementById('pwd-placeholder').innerText, this)"><i class="fa-solid fa-copy"></i> Copy</button>
                    </div>
                </div>
            </div>

            <div class="row border-top border-secondary pt-3 text-muted small">
                <div class="col-6">Server Type: <strong class="text-white"><?= htmlspecialchars($server['server_type']) ?></strong></div>
                <div class="col-6 text-end">Location: <strong class="text-white"><?= htmlspecialchars($server['location']) ?></strong></div>
            </div>
        </div>
    </div>

    <!-- Usage & Limits Box -->
    <div class="col-md-5">
        <div class="card-custom p-4 h-100">
            <h5 class="text-white mb-3"><i class="fa-solid fa-chart-line text-indigo me-2"></i>POP Usage Metrics</h5>

            <div class="bg-dark p-3 rounded border border-secondary mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-white small">Daily POP Capacity</span>
                    <span class="text-indigo font-monospace fw-bold"><?= number_format($popMetrics['daily_used']) ?> / <?= number_format($popMetrics['daily_limit']) ?></span>
                </div>
                <div class="progress bg-secondary" style="height: 8px;">
                    <?php $dPct = $popMetrics['daily_limit'] > 0 ? min(100, ($popMetrics['daily_used'] / $popMetrics['daily_limit']) * 100) : 0; ?>
                    <div class="progress-bar bg-indigo" style="width: <?= $dPct ?>%"></div>
                </div>
                <span class="text-muted small mt-2 d-block">Remaining Daily: <strong><?= number_format($popMetrics['daily_remaining']) ?></strong></span>
            </div>

            <div class="bg-dark p-3 rounded border border-secondary mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-white small">Monthly POP Capacity</span>
                    <span class="text-indigo font-monospace fw-bold"><?= number_format($popMetrics['monthly_used']) ?> / <?= number_format($popMetrics['monthly_limit']) ?></span>
                </div>
                <div class="progress bg-secondary" style="height: 8px;">
                    <?php $mPct = $popMetrics['monthly_limit'] > 0 ? min(100, ($popMetrics['monthly_used'] / $popMetrics['monthly_limit']) * 100) : 0; ?>
                    <div class="progress-bar bg-accent" style="width: <?= $mPct ?>%"></div>
                </div>
                <span class="text-muted small mt-2 d-block">Remaining Monthly: <strong><?= number_format($popMetrics['monthly_remaining']) ?></strong></span>
            </div>

            <div class="border-top border-secondary pt-3 text-muted small">
                <p class="mb-1"><i class="fa-solid fa-calendar text-indigo me-1"></i> Assigned: <?= date('Y-m-d', strtotime($server['assigned_at'])) ?></p>
                <p class="mb-0"><i class="fa-solid fa-clock text-warning me-1"></i> Expiration: <?= date('Y-m-d', strtotime($server['expiration_date'])) ?></p>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/user.php';
?>
