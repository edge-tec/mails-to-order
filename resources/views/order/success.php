<?php
ob_start();
?>
<div class="row justify-content-center py-5">
    <div class="col-md-7 text-center">
        <div class="card-custom p-5">
            <i class="fa-solid fa-circle-check text-success fa-5x mb-3"></i>
            <h2 class="text-white fw-bold">Order Submitted Successfully!</h2>
            <p class="lead text-muted mb-4">Your order reference number is <strong class="text-indigo font-monospace fs-4 d-block mt-2"><?= htmlspecialchars($order['order_number']) ?></strong></p>

            <div class="alert alert-dark border border-secondary text-start mb-4">
                <p class="mb-0 small text-light">Your server order has been submitted successfully. Our administrator will review your payment and activate your server after confirmation.</p>
            </div>

            <div class="d-flex justify-content-center gap-3">
                <a href="/orders/<?= $order['id'] ?>" class="btn btn-primary"><i class="fa-solid fa-receipt me-2"></i>Track Order Status</a>
                <a href="/dashboard" class="btn btn-outline-light"><i class="fa-solid fa-gauge me-2"></i>Go to Dashboard</a>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
