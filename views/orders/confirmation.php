<?php
// views/orders/confirmation.php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Order Confirmed';
?>
<?php require __DIR__ . '/../_header.php'; ?>

<div class="container" style="max-width:700px; text-align:center; padding-top:3rem;">
    <div style="font-size:4rem; margin-bottom:1rem;">🎉</div>
    <h1 style="font-size:1.8rem; font-weight:700; margin-bottom:.5rem;">Order Confirmed!</h1>
    <p style="color:var(--muted); margin-bottom:2rem;">
        Thank you for your purchase, <strong><?= htmlspecialchars($_SESSION['name'] ?? '') ?></strong>!
    </p>

    <div class="card" style="text-align:left; margin-bottom:1.5rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.2rem;">
            <div>
                <div style="font-size:.78rem; color:var(--muted); text-transform:uppercase; letter-spacing:.05em;">Order ID</div>
                <div style="font-family:'DM Mono',monospace; font-size:1.2rem; font-weight:700; color:var(--accent);">
                    #<?= (int)$order['id'] ?>
                </div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:.78rem; color:var(--muted); text-transform:uppercase; letter-spacing:.05em;">Status</div>
                <span style="background:rgba(82,201,127,.15); color:var(--success); border:1px solid rgba(82,201,127,.3);
                             padding:3px 10px; border-radius:20px; font-size:.82rem; font-weight:600;">
                    Pending
                </span>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:.75rem; margin-bottom:1.2rem; padding:.9rem; background:var(--surface2); border-radius:8px;">
            <div>
                <div style="font-size:.78rem; color:var(--muted);">Payment Method</div>
                <div style="font-weight:600; margin-top:2px;">
                    <?= $order['payment_method'] === 'cash_on_delivery' ? '💵 Cash on Delivery' : '💳 Online Wallet' ?>
                </div>
            </div>
            <div>
                <div style="font-size:.78rem; color:var(--muted);">Order Date</div>
                <div style="font-weight:600; margin-top:2px; font-family:'DM Mono',monospace;">
                    <?= date('M d, Y H:i', strtotime($order['order_date'])) ?>
                </div>
            </div>
        </div>

        <h3 style="font-size:.85rem; color:var(--muted); text-transform:uppercase; letter-spacing:.05em; margin-bottom:.75rem;">Items Ordered</h3>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th style="text-align:center;">Qty</th>
                        <th style="text-align:right;">Unit Price</th>
                        <th style="text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order['items'] as $item): ?>
                    <tr>
                        <td style="font-weight:600;"><?= htmlspecialchars($item['product_name']) ?></td>
                        <td style="text-align:center; font-family:'DM Mono',monospace;"><?= (int)$item['quantity'] ?></td>
                        <td style="text-align:right; font-family:'DM Mono',monospace;">৳<?= number_format($item['unit_price'], 2) ?></td>
                        <td style="text-align:right; font-family:'DM Mono',monospace;">৳<?= number_format($item['unit_price'] * $item['quantity'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align:right; font-weight:700; padding-top:.75rem;">Total Paid</td>
                        <td style="text-align:right; font-weight:700; font-size:1.1rem; color:var(--accent); font-family:'DM Mono',monospace; padding-top:.75rem;">
                            ৳<?= number_format($order['total_amount'], 2) ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div style="display:flex; gap:1rem; justify-content:center;">
        <a href="/task4_23-51148-1/views/product_details.php?id=1" class="btn btn-primary">Continue Shopping</a>
        <a href="/task4_23-51148-1/public/test_login.php" class="btn" style="background:var(--surface2); color:var(--text);">My Orders</a>
    </div>
</div>
