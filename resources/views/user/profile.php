<?php
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card-custom p-4">
            <h3 class="text-white mb-4"><i class="fa-solid fa-user-gear text-indigo me-2"></i>My Profile Details</h3>

            <form method="POST" action="/profile">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label text-white">Full Name</label>
                    <input type="text" name="name" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-white">Email Address (Read-only)</label>
                    <input type="email" class="form-control bg-dark text-muted border-secondary" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label text-white">Phone Number</label>
                    <input type="text" name="phone" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($user['phone']) ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label text-white">Full Billing Address</label>
                    <textarea name="address" class="form-control bg-dark text-white border-secondary" rows="3"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary px-4">Update Profile</button>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/user.php';
?>
