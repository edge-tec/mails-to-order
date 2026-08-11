<?php
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card-custom p-4">
            <h3 class="text-white mb-4"><i class="fa-solid fa-key text-indigo me-2"></i>Change Account Password</h3>

            <form method="POST" action="/security">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label text-white">Current Password</label>
                    <input type="password" name="current_password" class="form-control bg-dark text-white border-secondary" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-white">New Password</label>
                    <input type="password" name="new_password" class="form-control bg-dark text-white border-secondary" required>
                </div>
                <div class="mb-4">
                    <label class="form-label text-white">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control bg-dark text-white border-secondary" required>
                </div>
                <button type="submit" class="btn btn-primary px-4">Update Password</button>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/user.php';
?>
