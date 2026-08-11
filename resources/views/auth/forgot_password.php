<?php
ob_start();
?>
<div class="row justify-content-center py-5">
    <div class="col-md-5">
        <div class="card-custom p-4">
            <h3 class="text-white text-center mb-3">Forgot Password</h3>
            <p class="text-muted text-center small mb-4">Enter your registered email address to receive a password reset link.</p>

            <form method="POST" action="/forgot-password">
                <?= csrf_field() ?>
                <div class="mb-4">
                    <label class="form-label text-white">Email Address</label>
                    <input type="email" name="email" class="form-control bg-dark text-white border-secondary" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 mb-3">Send Reset Link</button>
            </form>
            <div class="text-center">
                <a href="/login" class="small text-muted text-decoration-none"><i class="fa-solid fa-arrow-left me-1"></i> Back to Login</a>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
