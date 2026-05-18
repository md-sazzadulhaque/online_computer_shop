<?php
// views/admin/customers.php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Manage Customers';
?>
<?php require __DIR__ . '/../_header.php'; ?>

<div class="container">
    <div class="page-header">
        <h1>Customer Management</h1>
        <p>Remove customers — deletes their reviews, cart, and orders</p>
    </div>

    <div id="customers-flash"></div>

    <div class="card">
        <?php if (empty($customers)): ?>
            <p style="color:var(--muted); font-size:.9rem;">No customers registered yet.</p>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Registered</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="customers-tbody">
                    <?php foreach ($customers as $c): ?>
                    <tr id="customer-row-<?= (int)$c['id'] ?>">
                        <td style="font-family:'DM Mono',monospace; color:var(--muted);"><?= (int)$c['id'] ?></td>
                        <td style="font-weight:600;"><?= htmlspecialchars($c['name']) ?></td>
                        <td style="color:var(--muted); font-size:.88rem;"><?= htmlspecialchars($c['email']) ?></td>
                        <td style="color:var(--muted); font-size:.85rem;"><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
                        <td>
                            <button class="btn btn-danger btn-sm delete-customer-btn" data-id="<?= (int)$c['id'] ?>">
                                Delete Customer
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
    const flash = document.getElementById('customers-flash');

    function showFlash(msg, type = 'error') {
        flash.innerHTML = `<div class="flash flash-${type}">${msg}</div>`;
        setTimeout(() => flash.innerHTML = '', 4000);
    }

    document.querySelectorAll('.delete-customer-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const name = btn.closest('tr').querySelector('td:nth-child(2)').textContent.trim();
            if (!confirm(`Delete customer "${name}"?\n\nThis will also remove all their reviews, cart, and orders. This action cannot be undone.`)) return;

            btn.disabled    = true;
            btn.textContent = 'Deleting…';

            const id       = btn.dataset.id;
            const formData = new FormData();
            formData.append('customer_id', id);

            try {
                const res  = await fetch('/task4_23-51148-1/public/api/admin/delete_customer.php', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    document.getElementById('customer-row-' + id)?.remove();
                    showFlash('Customer deleted successfully.', 'success');
                } else {
                    showFlash(data.error || 'Failed to delete customer.', 'error');
                    btn.disabled    = false;
                    btn.textContent = 'Delete Customer';
                }
            } catch {
                showFlash('Network error.', 'error');
                btn.disabled    = false;
                btn.textContent = 'Delete Customer';
            }
        });
    });
})();
</script>
