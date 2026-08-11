<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-white mb-0"><i class="fa-solid fa-envelope-open-text text-indigo me-2"></i>HTML Email Notification Templates</h3>
</div>

<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Key</th>
                    <th>Template Title</th>
                    <th>Email Subject</th>
                    <th>Last Updated</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($templates as $t): ?>
                    <tr>
                        <td class="font-monospace text-indigo"><?= htmlspecialchars($t['template_key']) ?></td>
                        <td class="fw-bold text-white"><?= htmlspecialchars($t['title']) ?></td>
                        <td><?= htmlspecialchars($t['subject']) ?></td>
                        <td class="small"><?= date('Y-m-d H:i', strtotime($t['updated_at'])) ?></td>
                        <td>
                            <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editTplModal_<?= $t['id'] ?>"><i class="fa-solid fa-pen me-1"></i> Edit Template</button>
                        </td>
                    </tr>

                    <!-- Edit Template Modal -->
                    <div class="modal fade" id="editTplModal_<?= $t['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content bg-dark text-white border-secondary">
                                <div class="modal-header border-secondary">
                                    <h5 class="modal-title">Edit Email Template — <?= htmlspecialchars($t['title']) ?></h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" action="/admin/email-templates/<?= $t['id'] ?>/edit">
                                    <?= csrf_field() ?>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Template Title</label>
                                            <input type="text" name="title" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($t['title']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email Subject</label>
                                            <input type="text" name="subject" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($t['subject']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">HTML Body Content</label>
                                            <textarea name="body_html" class="form-control bg-dark text-white border-secondary font-monospace small" rows="8" required><?= htmlspecialchars($t['body_html']) ?></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-secondary">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save Template</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>
