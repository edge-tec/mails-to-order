<?php
ob_start();
?>
<div class="row justify-content-center py-4">
    <div class="col-md-6">
        <div class="card-custom p-4">
            <div class="text-center mb-4">
                <i class="fa-solid fa-user-plus text-indigo fa-3x mb-2"></i>
                <h3 class="text-white">Create Customer Account</h3>
                <p class="text-muted small">Register to order servers and access credential management</p>
            </div>

            <form method="POST" action="/register">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label text-white">Full Name</label>
                    <input type="text" name="name" class="form-control bg-dark text-white border-secondary" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-white">Email Address</label>
                    <input type="email" name="email" class="form-control bg-dark text-white border-secondary" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-white">Phone Number</label>
                    <input type="text" name="phone" class="form-control bg-dark text-white border-secondary" placeholder="+1 800-000-0000" required>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label text-white">Password</label>
                        <input type="password" name="password" class="form-control bg-dark text-white border-secondary" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control bg-dark text-white border-secondary" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 mb-3">Complete Registration</button>
            </form>

            <div class="text-center border-top border-secondary pt-3">
                <p class="text-muted small mb-0">Already registered? <a href="/login" class="text-indigo text-decoration-none fw-bold">Sign In Here</a></p>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
