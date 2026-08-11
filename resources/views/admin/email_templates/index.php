<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="text-white mb-0"><i class="fa-solid fa-envelope-open-text text-indigo me-2"></i>Automated Email Templates</h3>
        <p class="text-light-silver small mb-0">Manage and customize subject lines and HTML bodies for automated customer notification emails</p>
    </div>
</div>

<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="text-white">Template Title</th>
                    <th class="text-white">Code Key</th>
                    <th class="text-white">Subject Line</th>
                    <th class="text-white">Status</th>
                    <th class="text-white text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($templates)): ?>
                    <tr><td colspan="5" class="text-center text-light-silver py-4">No email templates found.</td></tr>
                <?php else: ?>
                    <?php foreach ($templates as $t): ?>
                        <tr>
                            <td class="fw-bold text-white"><?= htmlspecialchars($t['title'] ?? $t['template_key'] ?? '') ?></td>
                            <td><code><?= htmlspecialchars($t['template_key'] ?? '') ?></code></td>
                            <td class="text-white small"><?= htmlspecialchars($t['subject'] ?? '') ?></td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td class="text-end">
                                <button class="btn btn-outline-info btn-sm" 
                                    onclick='editTemplate(<?= json_encode($t, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                    <i class="fa-solid fa-pen-to-square me-1"></i> Edit Template
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for Edit Email Template -->
<div class="modal fade" id="emailTemplateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold text-white" id="tplModalTitle"><i class="fa-solid fa-pen-to-square text-indigo me-2"></i>Edit Automated Email Template</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="tplForm" method="POST" action="">
                <?= csrf_field() ?>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">Template Title</label>
                        <input type="text" name="title" id="tpl_title" class="form-control bg-secondary text-white border-secondary" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">Email Subject Line</label>
                        <input type="text" name="subject" id="tpl_subject" class="form-control bg-secondary text-white border-secondary" required>
                        <span class="text-light-silver small">Use placeHolders like {{order_number}}, {{customer_name}}, {{package_name}}</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">HTML Body Content</label>
                        <textarea name="body_html" id="tpl_body" class="form-control bg-secondary text-white border-secondary font-monospace" rows="10" required></textarea>
                    </div>
                </div>

                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-save me-1"></i> Save Template</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editTemplate(tpl) {
    document.getElementById('tplModalTitle').innerText = 'Edit Email Template — ' + (tpl.title || tpl.template_key);
    document.getElementById('tplForm').action = '/admin/email-templates/' + tpl.id + '/edit';
    document.getElementById('tpl_title').value = tpl.title || '';
    document.getElementById('tpl_subject').value = tpl.subject || '';
    document.getElementById('tpl_body').value = tpl.body_html || '';
    
    var modal = new bootstrap.Modal(document.getElementById('emailTemplateModal'));
    modal.show();
}
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/admin.php';
?>
