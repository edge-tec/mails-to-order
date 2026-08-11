<?php
ob_start();
?>
<div class="card-custom p-4 my-4">
    <h2 class="text-white mb-4">Terms of Service & Usage Policies</h2>
    <h5 class="text-indigo">1. Server Ordering & Payment Verification</h5>
    <p class="text-muted">All server package orders are subject to manual administrative review and payment transaction verification. Credentials will be issued upon successful approval.</p>

    <h5 class="text-indigo">2. Acceptable Use Policy</h5>
    <p class="text-muted">Users must comply with all national and international regulations. Provisioned servers must not be utilized for illegal activities or malicious traffic amplification.</p>

    <h5 class="text-indigo">3. POP Capacity & Quotas</h5>
    <p class="text-muted">Daily and monthly POP limits are strictly enforced according to the ordered package tier. Usage resets automatically per billing cycle.</p>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
