<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Server Provisioning System') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand text-white" href="/">
                <i class="fa-solid fa-server text-indigo me-2"></i><?= htmlspecialchars(config('app.name', 'Server Ops')) ?>
            </a>
            <button class="navbar-toggler navbar-dark" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link text-white" href="/">Home</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="/packages">Server Packages</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="/contact">Contact</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="/terms">Terms</a></li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <?php if ($u = auth_user()): ?>
                        <?php if (in_array($u['role'], ['admin', 'super_admin'])): ?>
                            <a href="/admin" class="btn btn-outline-warning btn-sm"><i class="fa-solid fa-user-shield me-1"></i> Admin Panel</a>
                        <?php endif; ?>
                        <a href="/dashboard" class="btn btn-primary btn-sm"><i class="fa-solid fa-gauge me-1"></i> Dashboard</a>
                        <a href="/logout" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-right-from-bracket"></i></a>
                    <?php else: ?>
                        <a href="/login" class="btn btn-outline-light btn-sm">Log In</a>
                        <a href="/register" class="btn btn-primary btn-sm">Get Started</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            <?php if ($msg = flash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show"><i class="fa-solid fa-check-circle me-2"></i><?= htmlspecialchars($msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>
            <?php if ($msg = flash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show"><i class="fa-solid fa-exclamation-circle me-2"></i><?= htmlspecialchars($msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </div>
    </main>

    <footer class="border-top border-secondary mt-5 py-4 text-center text-white small">
        <div class="container">
            <p class="mb-1 text-white">&copy; <?= date('Y') ?> <?= htmlspecialchars(config('app.name')) ?>. All rights reserved.</p>
            <p class="mb-0 text-white"><a href="/terms" class="text-white text-decoration-underline me-3">Privacy & Terms</a> <a href="/contact" class="text-white text-decoration-underline">Support Desk</a></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/main.js"></script>
</body>
</html>
