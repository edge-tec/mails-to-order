<?php
ob_start();
?>
<div class="row justify-content-center py-5">
    <div class="col-md-5">
        <div class="card-custom p-4">
            <div class="text-center mb-4">
                <i class="fa-solid fa-user-lock text-indigo fa-3x mb-2"></i>
                <h3 class="text-white">Account Login</h3>
                <p class="text-muted small">Sign in to manage your server orders and provisioned credentials</p>
            </div>

            <form method="POST" action="/login">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label text-white">Email Address</label>
                    <input type="email" name="email" class="form-control bg-dark text-white border-secondary" required autofocus>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <label class="form-label text-white">Password</label>
                        <a href="/forgot-password" class="small text-indigo text-decoration-none">Forgot password?</a>
                    </div>
                    <input type="password" name="password" class="form-control bg-dark text-white border-secondary" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 mb-3">Sign In</button>
            </form>

            <div class="text-center border-top border-secondary pt-3">
                <p class="text-muted small mb-0">Don't have an account? <a href="/register" class="text-indigo text-decoration-none fw-bold">Register Here</a></p>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
