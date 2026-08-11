<?php
ob_start();
?>
<div class="row justify-content-center py-5">
    <div class="col-md-5">
        <div class="card-custom p-4">
            <h3 class="text-white text-center mb-3">Reset Password</h3>

            <form method="POST" action="/reset-password">
                <?= csrf_field() ?>
                <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
                <input type="hidden" name="email" value="<?= htmlspecialchars($email ?? '') ?>">
                <div class="mb-3">
                    <label class="form-label text-white">New Password</label>
                    <input type="password" name="password" class="form-control bg-dark text-white border-secondary" required>
                </div>
                <div class="mb-4">
                    <label class="form-label text-white">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control bg-dark text-white border-secondary" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2">Update Password</button>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
