<?php
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card-custom p-4">
            <h3 class="text-white mb-2"><i class="fa-solid fa-sliders text-indigo me-2"></i>Request Custom Server Package</h3>
            <p class="text-muted small mb-4">Submit your specific server specifications and required daily/monthly POP capacity for an enterprise quote.</p>

            <form method="POST" action="/custom-package-request">
                <?= csrf_field() ?>
                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label text-white">Required Daily POP Limit</label>
                        <input type="number" name="required_daily_pop" class="form-control bg-dark text-white border-secondary" placeholder="e.g. 5000" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white">Required Monthly POP Limit</label>
                        <input type="number" name="required_monthly_pop" class="form-control bg-dark text-white border-secondary" placeholder="e.g. 50000" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Preferred Server Location / Region</label>
                    <select name="preferred_location" class="form-select bg-dark text-white border-secondary" required>
                        <option value="USA - Ashburn (East Coast)">USA — Ashburn (East Coast)</option>
                        <option value="USA - Los Angeles (West Coast)">USA — Los Angeles (West Coast)</option>
                        <option value="Europe - Frankfurt (Germany)">Europe — Frankfurt (Germany)</option>
                        <option value="Asia - Singapore (SG1)">Asia — Singapore (SG1)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Contact Email / Phone</label>
                    <input type="text" name="contact_info" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars(auth_user()['email'] ?? '') ?>" required>
                </div>

                <div class="mb-4">
                    <label class="form-label text-white">Additional Server / Network Requirements</label>
                    <textarea name="additional_requirements" class="form-control bg-dark text-white border-secondary" rows="4" placeholder="e.g. Dedicated IP block, special OS version, bandwidth requirements..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary px-4">Submit Custom Package Inquiry</button>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/user.php';
?>
