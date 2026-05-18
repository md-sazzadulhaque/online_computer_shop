<?php
// views/orders/checkout.php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Checkout';
?>
<?php require __DIR__ . '/../_header.php'; ?>

<div class="container" style="max-width:800px;">
    <div class="page-header">
        <h1>Checkout</h1>
        <p>Review your order and choose a payment method</p>
    </div>

    <div id="checkout-flash"></div>

    <!-- Order Summary -->
    <div class="card" style="margin-bottom:1.5rem;">
        <h2 style="font-size:1rem; font-weight:700; margin-bottom:1rem; color:var(--muted); text-transform:uppercase; letter-spacing:.05em;">Order Summary</h2>
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
                    <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td>
                            <div style="display:flex; align-items:center; gap:.75rem;">
                                <?php if (!empty($item['image_path'])): ?>
                                    <img src="/public/uploads/products/<?= htmlspecialchars($item['image_path']) ?>"
                                         alt="" style="width:44px; height:44px; object-fit:cover; border-radius:6px; background:var(--surface2);">
                                <?php else: ?>
                                    <div style="width:44px;height:44px;border-radius:6px;background:var(--surface2);display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:1.2rem;">📦</div>
                                <?php endif; ?>
                                <span style="font-weight:600;"><?= htmlspecialchars($item['name']) ?></span>
                            </div>
                        </td>
                        <td style="text-align:center; font-family:'DM Mono',monospace;"><?= (int)$item['quantity'] ?></td>
                        <td style="text-align:right; font-family:'DM Mono',monospace;">৳<?= number_format($item['price'], 2) ?></td>
                        <td style="text-align:right; font-family:'DM Mono',monospace;">৳<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align:right; font-weight:700; padding-top:1rem;">Total</td>
                        <td style="text-align:right; font-weight:700; font-size:1.1rem; font-family:'DM Mono',monospace; color:var(--accent); padding-top:1rem;">৳<?= number_format($total, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Payment Method -->
    <div class="card" style="margin-bottom:1.5rem;">
        <h2 style="font-size:1rem; font-weight:700; margin-bottom:1.2rem; color:var(--muted); text-transform:uppercase; letter-spacing:.05em;">Payment Method</h2>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;" id="payment-options">
            <label class="payment-option" for="pay-cod" style="cursor:pointer;">
                <input type="radio" name="payment_method" id="pay-cod" value="cash_on_delivery" checked style="display:none;">
                <div class="pay-card" style="border:2px solid var(--accent); border-radius:10px; padding:1.2rem; text-align:center; transition:all .2s;">
                    <div style="font-size:2rem; margin-bottom:.5rem;">💵</div>
                    <div style="font-weight:700; margin-bottom:.25rem;">Cash on Delivery</div>
                    <div style="font-size:.82rem; color:var(--muted);">Pay when you receive your order</div>
                </div>
            </label>
            <label class="payment-option" for="pay-wallet" style="cursor:pointer;">
                <input type="radio" name="payment_method" id="pay-wallet" value="online_wallet" style="display:none;">
                <div class="pay-card" style="border:2px solid var(--border); border-radius:10px; padding:1.2rem; text-align:center; transition:all .2s;">
                    <div style="font-size:2rem; margin-bottom:.5rem;">💳</div>
                    <div style="font-weight:700; margin-bottom:.25rem;">Online Wallet</div>
                    <div style="font-size:.82rem; color:var(--muted);">Pay instantly with your e-wallet</div>
                </div>
            </label>
        </div>
    </div>

    <button id="place-order-btn" class="btn btn-primary" style="width:100%; padding:.85rem; font-size:1rem; justify-content:center;">
        Place Order
    </button>
    <a href="/task4_23-51148-1/views/cart.php" style="display:block; text-align:center; margin-top:1rem; color:var(--muted); font-size:.88rem;">← Back to Cart</a>
</div>

<style>
.payment-option input:checked + .pay-card {
    border-color: var(--accent);
    background: rgba(108,99,255,.08);
}
.pay-card { transition: border-color .2s, background .2s; }
.pay-card:hover { border-color: var(--accent) !important; }
</style>

<script>
// Payment option toggle styling
document.querySelectorAll('.payment-option input').forEach(radio => {
    radio.addEventListener('change', () => {
        document.querySelectorAll('.pay-card').forEach(c => {
            c.style.borderColor = 'var(--border)';
            c.style.background  = '';
        });
        if (radio.checked) {
            const card = radio.nextElementSibling;
            card.style.borderColor = 'var(--accent)';
            card.style.background  = 'rgba(108,99,255,.08)';
        }
    });
});
document.getElementById('place-order-btn').addEventListener('click', function () {

    const flashEl = document.getElementById('checkout-flash');
    const btn     = document.getElementById('place-order-btn');

    const selectedPayment =
        document.querySelector('input[name="payment_method"]:checked')?.value;

    if (!selectedPayment) {
        flashEl.innerHTML = '<div class="flash flash-error">Please select a payment method.</div>';
        return;
    }

    btn.disabled = true;
    btn.textContent = 'Placing Order…';

    const formData = new FormData();
    formData.append('payment_method', selectedPayment);

    

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/task4_23-51148-1/public/api/orders/place.php', true);

    xhr.onload = function () {
        var data = JSON.parse(xhr.responseText);

        if (data.success) {
            window.location.href =
                '/task4_23-51148-1/public/orders/confirmation.php?id=' + data.order_id;
        } else {
            flashEl.innerHTML =
                '<div class="flash flash-error">' +
                (data.error || 'Failed to place order.') +
                '</div>';

            btn.disabled = false;
            btn.textContent = 'Place Order';
        }
    };

    xhr.onerror = function () {
        flashEl.innerHTML =
            '<div class="flash flash-error">Network error. Please try again.</div>';

        btn.disabled = false;
        btn.textContent = 'Place Order';
    };

    xhr.send(formData);
});
</script>
