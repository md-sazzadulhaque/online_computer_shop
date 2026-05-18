<?php
// views/admin/reviews.php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Manage Reviews';
?>
<?php require __DIR__ . '/../_header.php'; ?>

<div class="container">
    <div class="page-header">
        <h1>Review Management</h1>
        <p>Delete any customer review from the platform</p>
    </div>

    <div id="reviews-flash"></div>

    <div class="card">
        <?php if (empty($reviews)): ?>
            <p style="color:var(--muted); font-size:.9rem;">No reviews yet.</p>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Reviewer</th>
                        <th>Comment</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reviews as $rv): ?>
                    <tr id="review-row-<?= (int)$rv['id'] ?>">
                        <td style="font-family:'DM Mono',monospace; color:var(--muted);"><?= (int)$rv['id'] ?></td>
                        <td style="font-weight:600;"><?= htmlspecialchars($rv['product_name']) ?></td>
                        <td><?= htmlspecialchars($rv['reviewer_name']) ?></td>
                        <td style="color:var(--muted); font-size:.88rem; max-width:300px;">
                            <?= htmlspecialchars(mb_strimwidth($rv['comment'], 0, 100, '…')) ?>
                        </td>
                        <td style="color:var(--muted); font-size:.85rem; white-space:nowrap;">
                            <?= date('M d, Y', strtotime($rv['created_at'])) ?>
                        </td>
                        <td>
                            <button class="btn btn-danger btn-sm delete-review-btn" data-id="<?= (int)$rv['id'] ?>">
                                Delete
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
(function () {
    const flash = document.getElementById('reviews-flash');

    function showFlash(msg, type = 'error') {
        flash.innerHTML = `<div class="flash flash-${type}">${msg}</div>`;
        setTimeout(() => flash.innerHTML = '', 4000);
    }

    document.querySelectorAll('.delete-review-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            if (!confirm('Delete this review permanently?')) return;
            btn.disabled    = true;
            btn.textContent = '…';

            const id       = btn.dataset.id;
            const formData = new FormData();
            formData.append('review_id', id);

            try {
                const res  = await fetch('/task4_23-51148-1/public/api/admin/delete_review.php', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    document.getElementById('review-row-' + id)?.remove();
                    showFlash('Review deleted.', 'success');
                } else {
                    showFlash(data.error || 'Failed to delete.', 'error');
                    btn.disabled    = false;
                    btn.textContent = 'Delete';
                }
            } catch {
                showFlash('Network error.', 'error');
                btn.disabled    = false;
                btn.textContent = 'Delete';
            }
        });
    });
})();
</script>
