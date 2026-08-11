<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Admin Console') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand text-warning" href="/admin">
                <i class="fa-solid fa-user-shield me-2"></i><?= htmlspecialchars(config('app.name')) ?> — Admin Console
            </a>
            <div class="d-flex align-items-center gap-3 ms-auto">
                <span class="badge bg-warning text-dark"><i class="fa-solid fa-shield-halved me-1"></i> <?= htmlspecialchars(auth_user()['role'] ?? 'Admin') ?></span>
                <a href="/dashboard" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-user me-1"></i> User Portal</a>
                <a href="/logout" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 px-0 sidebar d-none d-md-block">
                <div class="p-3">
                    <div class="nav flex-column">
                        <a href="/admin" class="nav-link <?= $_SERVER['REQUEST_URI'] === '/admin' ? 'active' : '' ?>"><i class="fa-solid fa-chart-line me-2"></i> Overview</a>
                        <a href="/admin/users" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false ? 'active' : '' ?>"><i class="fa-solid fa-users me-2"></i> User Accounts</a>
                        <a href="/admin/orders" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/orders') !== false ? 'active' : '' ?>"><i class="fa-solid fa-cart-shopping me-2"></i> Orders Management</a>
                        <a href="/admin/servers" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/servers') !== false ? 'active' : '' ?>"><i class="fa-solid fa-server me-2"></i> Server Inventory</a>
                        <a href="/admin/packages" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/packages') !== false ? 'active' : '' ?>"><i class="fa-solid fa-box-open me-2"></i> Packages & Pricing</a>
                        <a href="/admin/payment-methods" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/payment-methods') !== false ? 'active' : '' ?>"><i class="fa-solid fa-credit-card me-2"></i> Payment Settings</a>
                        <a href="/admin/custom-packages" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/custom-packages') !== false ? 'active' : '' ?>"><i class="fa-solid fa-sliders me-2"></i> Custom Requests</a>
                        <a href="/admin/email-templates" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/email-templates') !== false ? 'active' : '' ?>"><i class="fa-solid fa-envelope-open-text me-2"></i> Email Templates</a>
                        <a href="/admin/settings" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/settings') !== false ? 'active' : '' ?>"><i class="fa-solid fa-gears me-2"></i> System Settings</a>
                        <a href="/admin/audit-logs" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/audit-logs') !== false ? 'active' : '' ?>"><i class="fa-solid fa-file-shield me-2"></i> Audit Logs</a>
                    </div>
                </div>
            </div>

            <div class="col-md-9 col-lg-10 p-4">
                <?php if ($msg = flash('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show"><i class="fa-solid fa-check-circle me-2"></i><?= htmlspecialchars($msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>
                <?php if ($msg = flash('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show"><i class="fa-solid fa-exclamation-circle me-2"></i><?= htmlspecialchars($msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>

                <?= $content ?? '' ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/assets/js/main.js"></script>
</body>
</html>
