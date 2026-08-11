<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'User Dashboard') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand text-white fw-bold" href="/dashboard">
                <i class="fa-solid fa-server text-indigo me-2"></i><?= htmlspecialchars(config('app.name')) ?>
            </a>
            <div class="d-flex align-items-center gap-3 ms-auto">
                <span class="text-light fw-medium small d-none d-md-inline"><i class="fa-solid fa-user me-1"></i><?= htmlspecialchars(auth_user()['name'] ?? '') ?></span>
                <a href="/logout" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-right-from-bracket me-1"></i> Log Out</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 px-0 sidebar d-none d-md-block">
                <div class="p-3">
                    <div class="nav flex-column">
                        <a href="/dashboard" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false ? 'active' : '' ?>"><i class="fa-solid fa-gauge me-2"></i> Dashboard</a>
                        <a href="/orders" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/orders') !== false ? 'active' : '' ?>"><i class="fa-solid fa-receipt me-2"></i> My Orders</a>
                        <a href="/servers" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/servers') !== false ? 'active' : '' ?>"><i class="fa-solid fa-network-wired me-2"></i> My Servers</a>
                        <a href="/custom-package-request" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/custom-package-request') !== false ? 'active' : '' ?>"><i class="fa-solid fa-sliders me-2"></i> Custom Package</a>
                        <a href="/profile" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/profile') !== false ? 'active' : '' ?>"><i class="fa-solid fa-user-gear me-2"></i> Profile</a>
                        <a href="/security" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/security') !== false ? 'active' : '' ?>"><i class="fa-solid fa-key me-2"></i> Security</a>
                        <a href="/contact" class="nav-link"><i class="fa-solid fa-headset me-2"></i> Support</a>
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
    <script src="/assets/js/main.js"></script>
</body>
</html>
