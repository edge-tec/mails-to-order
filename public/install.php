<?php

require_once __DIR__ . '/../vendor/autoload.php';
if (file_exists(__DIR__ . '/../database/seeders/DatabaseSeeder.php')) {
    require_once __DIR__ . '/../database/seeders/DatabaseSeeder.php';
}

use App\Services\InstallerService;
use App\Services\Database;
use Database\Seeders\DatabaseSeeder;

session_start();

$lockFile = __DIR__ . '/../storage/installed.lock';
if (file_exists($lockFile)) {
    die("<h1>System Already Installed</h1><p>The Server Provisioning Portal is already installed. For security reasons, <code>install.php</code> is locked. Remove <code>storage/installed.lock</code> if you need to reinstall.</p>");
}

$step = (int)($_GET['step'] ?? 1);
$error = null;
$success = null;

// Redirect to Step 2 if user directly accesses Step 3+ without entering DB info
if ($step > 2 && empty($_SESSION['install']['db'])) {
    header('Location: install.php?step=2');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'step2_database') {
        $dbHost = trim($_POST['db_host'] ?? '127.0.0.1');
        $dbPort = trim($_POST['db_port'] ?? '3306');
        $dbName = trim($_POST['db_name'] ?? 'server_ordering_db');
        $dbUser = trim($_POST['db_user'] ?? 'root');
        $dbPass = $_POST['db_pass'] ?? '';

        try {
            InstallerService::testDatabase([
                'host' => $dbHost,
                'port' => $dbPort,
                'database' => $dbName,
                'username' => $dbUser,
                'password' => $dbPass
            ]);

            $_SESSION['install']['db'] = [
                'host' => $dbHost,
                'port' => $dbPort,
                'name' => $dbName,
                'user' => $dbUser,
                'pass' => $dbPass
            ];

            header('Location: install.php?step=3');
            exit;
        } catch (Exception $e) {
            $error = "Database Connection Failed: " . $e->getMessage();
        }
    }

    if ($action === 'step3_migrate') {
        try {
            $dbConfig = $_SESSION['install']['db'] ?? [];
            if (empty($dbConfig)) {
                header('Location: install.php?step=2');
                exit;
            }
            InstallerService::runMigrationsAndSeeders($dbConfig);
            header('Location: install.php?step=4');
            exit;
        } catch (Exception $e) {
            $error = "Migration Failed: " . $e->getMessage();
        }
    }

    if ($action === 'step4_admin') {
        $adminName = trim($_POST['admin_name'] ?? '');
        $adminEmail = strtolower(trim($_POST['admin_email'] ?? ''));
        $adminPass = $_POST['admin_pass'] ?? '';

        if (empty($adminName) || empty($adminEmail) || empty($adminPass)) {
            $error = "All Administrator fields are required.";
        } else {
            $_SESSION['install']['admin'] = [
                'name' => $adminName,
                'email' => $adminEmail,
                'pass' => $adminPass
            ];
            header('Location: install.php?step=5');
            exit;
        }
    }

    if ($action === 'step5_site') {
        $siteName = trim($_POST['site_name'] ?? 'Server Provisioning Portal');
        $siteUrl = rtrim(trim($_POST['site_url'] ?? 'http://localhost:8000'), '/');
        $companyName = trim($_POST['company_name'] ?? 'Edge-Tec Server Solutions');

        $_SESSION['install']['site'] = [
            'name' => $siteName,
            'url' => $siteUrl,
            'company' => $companyName
        ];
        header('Location: install.php?step=6');
        exit;
    }

    if ($action === 'step6_smtp') {
        $smtpHost = trim($_POST['smtp_host'] ?? 'smtp.mailtrap.io');
        $smtpPort = trim($_POST['smtp_port'] ?? '2525');
        $smtpUser = trim($_POST['smtp_user'] ?? '');
        $smtpPass = $_POST['smtp_pass'] ?? '';
        $smtpEnc = trim($_POST['smtp_enc'] ?? 'tls');
        $fromAddr = trim($_POST['mail_from_addr'] ?? 'support@example.com');
        $fromName = trim($_POST['mail_from_name'] ?? $_SESSION['install']['site']['name'] ?? 'Server Operations');

        $dbConfig = $_SESSION['install']['db'] ?? [];
        $admin = $_SESSION['install']['admin'] ?? [];
        $site = $_SESSION['install']['site'] ?? [];

        try {
            // Write .env
            InstallerService::generateEnvFile([
                'site_name' => $site['name'] ?? 'Server Provisioning Portal',
                'site_url' => $site['url'] ?? 'http://localhost:8000',
                'db_host' => $dbConfig['host'] ?? '127.0.0.1',
                'db_port' => $dbConfig['port'] ?? '3306',
                'db_name' => $dbConfig['name'] ?? 'server_ordering_db',
                'db_user' => $dbConfig['user'] ?? 'root',
                'db_pass' => $dbConfig['pass'] ?? '',
                'smtp_host' => $smtpHost,
                'smtp_port' => $smtpPort,
                'smtp_user' => $smtpUser,
                'smtp_pass' => $smtpPass,
                'smtp_enc' => $smtpEnc,
                'mail_from_addr' => $fromAddr,
                'mail_from_name' => $fromName,
            ]);

            // Run Seeder
            DatabaseSeeder::run();

            // Create Administrator Account
            $adminUser = Database::fetch("SELECT id FROM users WHERE email = ?", [$admin['email'] ?? 'admin@example.com']);
            if (!$adminUser) {
                $userId = Database::insert(
                    "INSERT INTO users (name, email, phone, address, password_hash, role, status, email_verified_at, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, 'super_admin', 'active', NOW(), NOW(), NOW())",
                    [$admin['name'] ?? 'System Administrator', $admin['email'] ?? 'admin@example.com', '+1 800-555-0199', 'Headquarters', password_hash($admin['pass'] ?? 'Admin@123456', PASSWORD_BCRYPT)]
                );
                Database::insert("INSERT INTO admins (user_id, department, created_at) VALUES (?, 'Super Administration', NOW())", [$userId]);
            }

            // Create installed lock file
            file_put_contents($lockFile, date('Y-m-d H:i:s'));

            header('Location: install.php?step=7');
            exit;
        } catch (Exception $e) {
            $error = "Finalizing Installation Failed: " . $e->getMessage();
        }
    }
}

$reqs = InstallerService::checkRequirements();
$allReqsPassed = true;
foreach ($reqs as $r) {
    if (!$r['status']) $allReqsPassed = false;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation Wizard — Server Provisioning System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #0f172a; color: #f8fafc; font-family: 'Inter', system-ui, -apple-system, sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .install-card { background: #1e293b; border: 1px solid #334155; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5); max-width: 650px; width: 100%; padding: 40px; }
        .step-progress { display: flex; justify-content: space-between; margin-bottom: 30px; border-bottom: 1px solid #334155; padding-bottom: 20px; }
        .step-item { font-size: 13px; font-weight: 600; color: #64748b; }
        .step-item.active { color: #6366f1; }
        .step-item.completed { color: #10b981; }
        .btn-primary { background: #6366f1; border-color: #6366f1; }
        .btn-primary:hover { background: #4f46e5; }
        .form-control, .form-select { background: #0f172a; border-color: #334155; color: #ffffff; }
        .form-control:focus, .form-select:focus { background: #0f172a; border-color: #6366f1; color: #ffffff; box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.25); }
    </style>
</head>
<body>
    <div class="install-card">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-white"><i class="fa-solid fa-server text-indigo me-2"></i>Server System Installer</h3>
            <p class="text-secondary small">Production Setup & Configuration Wizard</p>
        </div>

        <div class="step-progress">
            <span class="step-item <?= $step === 1 ? 'active' : ($step > 1 ? 'completed' : '') ?>">1. Check</span>
            <span class="step-item <?= $step === 2 ? 'active' : ($step > 2 ? 'completed' : '') ?>">2. DB</span>
            <span class="step-item <?= $step === 3 ? 'active' : ($step > 3 ? 'completed' : '') ?>">3. Tables</span>
            <span class="step-item <?= $step === 4 ? 'active' : ($step > 4 ? 'completed' : '') ?>">4. Admin</span>
            <span class="step-item <?= $step === 5 ? 'active' : ($step > 5 ? 'completed' : '') ?>">5. Site</span>
            <span class="step-item <?= $step === 6 ? 'active' : ($step > 6 ? 'completed' : '') ?>">6. SMTP</span>
            <span class="step-item <?= $step === 7 ? 'active' : '' ?>">7. Done</span>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($step === 1): ?>
            <h5 class="text-white mb-3">Step 1 — System Compatibility Check</h5>
            <ul class="list-group list-group-flush mb-4">
                <?php foreach ($reqs as $key => $r): ?>
                    <li class="list-group-item bg-transparent text-light border-secondary d-flex justify-content-between align-items-center">
                        <span><?= $r['name'] ?></span>
                        <?php if ($r['status']): ?>
                            <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i><?= $r['current'] ?></span>
                        <?php else: ?>
                            <span class="badge bg-danger"><i class="fa-solid fa-xmark me-1"></i><?= $r['current'] ?></span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="d-flex justify-content-end">
                <?php if ($allReqsPassed): ?>
                    <a href="install.php?step=2" class="btn btn-primary px-4">Continue to Database Setup <i class="fa-solid fa-arrow-right ms-2"></i></a>
                <?php else: ?>
                    <button class="btn btn-secondary px-4" disabled>Fix Requirements to Continue</button>
                <?php endif; ?>
            </div>

        <?php elseif ($step === 2): ?>
            <h5 class="text-white mb-3">Step 2 — Database Connection</h5>
            <form method="POST">
                <input type="hidden" name="action" value="step2_database">
                <div class="mb-3">
                    <label class="form-label">Database Host</label>
                    <input type="text" name="db_host" class="form-control" value="127.0.0.1" required>
                </div>
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Database Name</label>
                        <input type="text" name="db_name" class="form-control" value="server_ordering_db" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Port</label>
                        <input type="text" name="db_port" class="form-control" value="3306" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Database Username</label>
                    <input type="text" name="db_user" class="form-control" value="root" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Database Password</label>
                    <input type="password" name="db_pass" class="form-control">
                </div>
                <div class="d-flex justify-content-between">
                    <a href="install.php?step=1" class="btn btn-outline-secondary">Back</a>
                    <button type="submit" class="btn btn-primary px-4">Test & Connect Database</button>
                </div>
            </form>

        <?php elseif ($step === 3): ?>
            <h5 class="text-white mb-3">Step 3 — Initialize Database Schema & Seed Data</h5>
            <p class="text-secondary">Ready to build normalized MySQL tables (users, orders, servers, credentials, payment methods, audit logs) and populate default server packages.</p>
            <form method="POST">
                <input type="hidden" name="action" value="step3_migrate">
                <div class="d-flex justify-content-between">
                    <a href="install.php?step=2" class="btn btn-outline-secondary">Back</a>
                    <button type="submit" class="btn btn-primary px-4">Execute Migrations & Seeders</button>
                </div>
            </form>

        <?php elseif ($step === 4): ?>
            <h5 class="text-white mb-3">Step 4 — Create Super Admin Account</h5>
            <form method="POST">
                <input type="hidden" name="action" value="step4_admin">
                <div class="mb-3">
                    <label class="form-label">Administrator Full Name</label>
                    <input type="text" name="admin_name" class="form-control" value="System Administrator" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Admin Email Address</label>
                    <input type="email" name="admin_email" class="form-control" value="admin@example.com" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Admin Password</label>
                    <input type="password" name="admin_pass" class="form-control" value="Admin@123456" required>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="install.php?step=3" class="btn btn-outline-secondary">Back</a>
                    <button type="submit" class="btn btn-primary px-4">Save Administrator</button>
                </div>
            </form>

        <?php elseif ($step === 5): ?>
            <h5 class="text-white mb-3">Step 5 — Website Configuration</h5>
            <form method="POST">
                <input type="hidden" name="action" value="step5_site">
                <div class="mb-3">
                    <label class="form-label">Website Name</label>
                    <input type="text" name="site_name" class="form-control" value="Server Provisioning Portal" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Website Domain URL</label>
                    <input type="url" name="site_url" class="form-control" value="http://localhost:8000" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Company Name</label>
                    <input type="text" name="company_name" class="form-control" value="Edge-Tec Server Solutions" required>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="install.php?step=4" class="btn btn-outline-secondary">Back</a>
                    <button type="submit" class="btn btn-primary px-4">Continue to Mail Config</button>
                </div>
            </form>

        <?php elseif ($step === 6): ?>
            <h5 class="text-white mb-3">Step 6 — SMTP Mail Settings</h5>
            <form method="POST">
                <input type="hidden" name="action" value="step6_smtp">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">SMTP Host</label>
                        <input type="text" name="smtp_host" class="form-control" value="smtp.mailtrap.io" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">SMTP Port</label>
                        <input type="text" name="smtp_port" class="form-control" value="2525" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">SMTP Username</label>
                        <input type="text" name="smtp_user" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">SMTP Password</label>
                        <input type="password" name="smtp_pass" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Encryption</label>
                    <select name="smtp_enc" class="form-select">
                        <option value="tls" selected>TLS (Recommended)</option>
                        <option value="ssl">SSL</option>
                    </select>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">From Email Address</label>
                        <input type="email" name="mail_from_addr" class="form-control" value="noreply@example.com" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">From Name</label>
                        <input type="text" name="mail_from_name" class="form-control" value="Server Operations" required>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="install.php?step=5" class="btn btn-outline-secondary">Back</a>
                    <button type="submit" class="btn btn-primary px-4">Finish Installation & Lock</button>
                </div>
            </form>

        <?php elseif ($step === 7): ?>
            <div class="text-center py-4">
                <i class="fa-solid fa-circle-check text-success fa-4x mb-3"></i>
                <h4 class="text-white mb-2">Installation Complete!</h4>
                <p class="text-secondary mb-4">The Server Ordering & Provisioning System has been installed successfully. <code>.env</code> file generated and installer lock activated.</p>
                <div class="d-grid gap-2 col-8 mx-auto">
                    <a href="/login" class="btn btn-primary btn-lg"><i class="fa-solid fa-right-to-bracket me-2"></i>Log In to Admin Dashboard</a>
                    <a href="/" class="btn btn-outline-light"><i class="fa-solid fa-globe me-2"></i>View Public Portal</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
