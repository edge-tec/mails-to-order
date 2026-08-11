<?php
ob_start();
?>
<div class="text-center py-5">
    <i class="fa-solid fa-triangle-exclamation text-warning fa-5x mb-3"></i>
    <h1 class="display-3 text-white fw-bold">404</h1>
    <h3 class="text-muted mb-4">Page Not Found</h3>
    <a href="/" class="btn btn-primary px-4"><i class="fa-solid fa-house me-2"></i>Return Home</a>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
