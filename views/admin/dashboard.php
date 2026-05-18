<?php
// views/admin/dashboard.php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Admin Dashboard';
?>
<?php require __DIR__ . '/../_header.php'; ?>

<div class="container">
    <div class="page-header">
        <h1>Admin Dashboard</h1>
        <p>Task 4 — Recent Orders & Reviews overview</p>
    </div>

    <!-- Quick links -->
    <div style="display:flex; gap:.75rem; margin-bottom:2rem; flex-wrap:wrap;">
        <a href="/admin/customers.php" class="btn btn-primary">Manage Customers</a>
        <a href="/admin/reviews.php" class="btn" style="background:var(--surface2); color:var(--text);">Manage Reviews</a>
    </div>

    <!-- Recent Orders -->
    <div class="card" style="margin-bottom:1.5rem;">
        <h2 style="font-size:1rem; font-weight:700; margin-bottom:1rem; color:var(--muted); text-transform:uppercase; letter-spacing:.05em;">
            Recent Orders
        </h2>
        <?php if (empty($recentOrders)): ?>
            <p style="color:var(--muted); font-size:.9rem;">No orders yet.</p>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $ord): ?>
                    <tr>
                        <td style="font-family:'DM Mono',monospace; color:var(--accent);">#<?= (int)$ord['id'] ?></td>
                        <td><?= htmlspecialchars($ord['customer_name']) ?></td>
                        <td style="font-family:'DM Mono',monospace;">৳<?= number_format($ord['total_amount'], 2) ?></td>
                        <td><?= $ord['payment_method'] === 'cash_on_delivery' ? 'COD' : 'Wallet' ?></td>
                        <td>
                            <span style="padding:3px 9px; border-radius:20px; font-size:.78rem; font-weight:600;
                                background:rgba(82,201,127,.12); color:var(--success); border:1px solid rgba(82,201,127,.25);">
                                <?= htmlspecialchars(ucfirst($ord['status'])) ?>
                            </span>
                        </td>
                        <td style="color:var(--muted); font-size:.85rem;"><?= date('M d, Y', strtotime($ord['order_date'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Recent Reviews -->
    <div class="card">
        <h2 style="font-size:1rem; font-weight:700; margin-bottom:1rem; color:var(--muted); text-transform:uppercase; letter-spacing:.05em;">
            Recent Reviews
        </h2>
        <?php if (empty($recentReviews)): ?>
            <p style="color:var(--muted); font-size:.9rem;">No reviews yet.</p>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Reviewer</th>
                        <th>Comment</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentReviews as $rv): ?>
                    <tr id="dash-review-<?= (int)$rv['id'] ?>">
                        <td style="font-weight:600;"><?= htmlspecialchars($rv['product_name']) ?></td>
                        <td><?= htmlspecialchars($rv['reviewer_name']) ?></td>
                        <td style="color:var(--muted); font-size:.88rem;">
                            <?= htmlspecialchars(mb_strimwidth($rv['comment'], 0, 80, '…')) ?>
                        </td>
                        <td style="color:var(--muted); font-size:.85rem;"><?= date('M d, Y', strtotime($rv['created_at'])) ?></td>
                        <td>
                            <button class="btn btn-danger btn-sm delete-review-dash" data-id="<?= (int)$rv['id'] ?>">Delete</button>
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
document.querySelectorAll('.delete-review-dash').forEach(btn => {
    btn.addEventListener('click', async () => {
        if (!confirm('Delete this review?')) return;
        const id  = btn.dataset.id;
        const res = await fetch('/task4_23-51148-1/public/api/admin/delete_review.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'review_id=' + encodeURIComponent(id)
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById('dash-review-' + id)?.remove();
        } else {
            alert(data.error || 'Failed to delete review.');
        }
    });
});
</script>
