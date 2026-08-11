<?php
ob_start();
?>
<div class="row justify-content-center py-4">
    <div class="col-md-8">
        <div class="card-custom p-4">
            <h3 class="text-white mb-3"><i class="fa-solid fa-headset text-indigo me-2"></i>Contact Support</h3>
            <p class="text-muted">Have a question regarding server orders, payments, or custom POP capacities? Send us a message.</p>

            <form method="POST" action="/contact">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label text-white">Your Name</label>
                    <input type="text" name="name" class="form-control bg-dark text-white border-secondary" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-white">Your Email</label>
                    <input type="email" name="email" class="form-control bg-dark text-white border-secondary" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-white">Subject</label>
                    <input type="text" name="subject" class="form-control bg-dark text-white border-secondary" required>
                </div>
                <div class="mb-4">
                    <label class="form-label text-white">Message</label>
                    <textarea name="message" class="form-control bg-dark text-white border-secondary" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary px-4">Send Message</button>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
