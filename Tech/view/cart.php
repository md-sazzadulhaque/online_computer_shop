<?php
session_start();

if(!isset($_SESSION['customer_id'])){
    header('location: login.php?error=Please login to view your cart');
    exit;
}

require_once(__DIR__ . '/../model/cartModel.php');

$customer_id = $_SESSION['customer_id'];

$result    = getCartItems($customer_id);
$total     = getCartTotal($customer_id);
$cartCount = getCartCount($customer_id);

$cartItems = [];
while($row = mysqli_fetch_assoc($result)){
    $row['subtotal'] = $row['price'] * $row['quantity'];
    $cartItems[]     = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Cart - TechShop</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background-color: #f0f0f0; color: #222; }
        a { text-decoration: none; color: #222; }

        .navbar {
            background-color: #222;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .navbar .logo { color: white; font-size: 20px; font-weight: bold; }
        .navbar a { color: #ccc; font-size: 14px; }
        .navbar a:hover { color: white; }
        .navbar .cart-link {
            background-color: #e67e00;
            color: white !important;
            padding: 5px 12px;
            border-radius: 3px;
            font-weight: bold;
        }
        .navbar .nav-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 14px;
            color: #ccc;
            font-size: 14px;
        }
        .navbar .nav-right a { color: #ccc; }
        .navbar .nav-right a:hover { color: white; }

        .container {
            max-width: 900px;
            margin: 30px auto;
            background: white;
            border: 1px solid #ddd;
            padding: 30px;
        }

        h2 { font-size: 20px; margin-bottom: 20px; }

        #ajax-msg { margin-bottom: 14px; font-size: 14px; }
        .success { padding: 8px 12px; background: #e6ffe6; border: 1px solid green; color: green; }
        .error   { padding: 8px 12px; background: #ffe6e6; border: 1px solid red;   color: red;   }

        table { width: 100%; border-collapse: collapse; }
        table th {
            background-color: #222;
            color: white;
            padding: 10px 12px;
            text-align: left;
            font-size: 13px;
        }
        table td {
            padding: 10px 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            vertical-align: middle;
        }
        table tr:hover td { background-color: #fafafa; }

        .total-row td {
            font-weight: bold;
            font-size: 15px;
            background-color: #f5f5f5;
            border-top: 2px solid #ddd;
        }

        .qty-controls { display: flex; align-items: center; }
        .qty-btn {
            padding: 4px 10px;
            background-color: #ddd;
            border: 1px solid #ccc;
            cursor: pointer;
            font-size: 16px;
        }
        .qty-btn:hover { background-color: #bbb; }
        .qty-display {
            padding: 4px 12px;
            border-top: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
            font-size: 14px;
            font-weight: bold;
            min-width: 36px;
            text-align: center;
            background: white;
        }

        .btn-remove {
            padding: 5px 12px;
            background-color: #cc0000;
            color: white;
            font-size: 13px;
            border: none;
            cursor: pointer;
        }
        .btn-remove:hover { background-color: #990000; }

        .btn-back {
            display: inline-block;
            margin-top: 20px;
            padding: 8px 18px;
            background-color: #222;
            color: white;
            font-size: 13px;
        }
        .btn-back:hover { background-color: #444; }

        .empty-msg {
            text-align: center;
            color: #888;
            padding: 40px;
            font-size: 15px;
        }
        .empty-msg a { color: #222; text-decoration: underline; }
    </style>
</head>
<body>

<! NAVBAR>
<div class="navbar">
    <span class="logo">TechShop</span>
    <a href="home.php">Home</a>
    <a href="cart.php" class="cart-link">
        Cart (<span id="nav-cart-count"><?php echo $cartCount; ?></span>)
    </a>
    <div class="nav-right">
        Welcome, <b style="color:white"><?php echo $_SESSION['customer_name']; ?></b>
        &nbsp;|&nbsp;
        <a href="../controller/authController.php?logout=true">Logout</a>
    </div>
</div>


<div class="container">

    <h2>My Cart</h2>

    <!-- AJAX message box -->
    <div id="ajax-msg"></div>

    <?php if(empty($cartItems)): ?>

        <div class="empty-msg">
            Your cart is empty. <a href="home.php">Continue shopping</a>
        </div>

    <?php else: ?>

    <table>
        <tr>
            <th>Product Name</th>
            <th>Unit Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th>Remove</th>
        </tr>

        <?php foreach($cartItems as $item): ?>
        <tr id="cart-row-<?php echo $item['cart_id']; ?>">
            <td><?php echo $item['name']; ?></td>
            <td>Tk <?php echo number_format($item['price'], 2); ?></td>
            <td>
                <div class="qty-controls">
                    <button class="qty-btn" onclick="changeQty(<?php echo $item['cart_id']; ?>, -1)">−</button>
                    <span class="qty-display" id="qty-<?php echo $item['cart_id']; ?>">
                        <?php echo $item['quantity']; ?>
                    </span>
                    <button class="qty-btn" onclick="changeQty(<?php echo $item['cart_id']; ?>, 1)">+</button>
                </div>
            </td>
            <td id="sub-<?php echo $item['cart_id']; ?>">
                Tk <?php echo number_format($item['subtotal'], 2); ?>
            </td>
            <td>
                <button class="btn-remove" onclick="removeItem(<?php echo $item['cart_id']; ?>)">
                    Remove
                </button>
            </td>
        </tr>
        <?php endforeach; ?>

        <tr class="total-row">
            <td colspan="3" style="text-align:right;">Total</td>
            <td id="cart-total">Tk <?php echo $total; ?></td>
            <td></td>
        </tr>
    </table>

    <a class="btn-back" href="home.php">Continue Shopping</a>

    <?php endif; ?>

</div>
<script>
// Store each item's 
var itemData = {
    <?php foreach($cartItems as $item): ?>
    <?php echo $item['cart_id']; ?>: {
        qty:   <?php echo $item['quantity']; ?>,
        stock: <?php echo $item['stock']; ?>
    },
    <?php endforeach; ?>
};

// + or - button clicked
function changeQty(cart_id, delta){
    var current = itemData[cart_id].qty;
    var stock   = itemData[cart_id].stock;
    var newQty  = current + delta;

    // JS validation
    if(newQty < 1){
        showMsg('Quantity cannot be less than 1', 'error');
        return;
    }
    if(newQty > stock){
        showMsg('Cannot exceed available stock (' + stock + ')', 'error');
        return;
    }

    // Update display immediately
    itemData[cart_id].qty = newQty;
    document.getElementById('qty-' + cart_id).textContent = newQty;

    // Send AJAX update
    sendAjax('update', { cart_id: cart_id, quantity: newQty }, function(data){
        if(data.status === 'success'){
            // Update each subtotal shown on page
            for(var i = 0; i < data.items.length; i++){
                var item  = data.items[i];
                var subEl = document.getElementById('sub-' + item.cart_id);
                if(subEl) subEl.textContent = 'Tk ' + parseFloat(item.subtotal).toFixed(2);
            }
            document.getElementById('cart-total').textContent      = 'Tk ' + data.total;
            document.getElementById('nav-cart-count').textContent  = data.cart_count;
        } else {
            showMsg(data.message, 'error');
        }
    });
}

// Remove button clicked
function removeItem(cart_id){
    if(!confirm('Remove this item from cart?')) return;

    sendAjax('remove', { cart_id: cart_id }, function(data){
        if(data.status === 'success'){
            // Remove row from table
            var row = document.getElementById('cart-row-' + cart_id);
            if(row) row.remove();
            delete itemData[cart_id];

            document.getElementById('cart-total').textContent     = 'Tk ' + data.total;
            document.getElementById('nav-cart-count').textContent = data.cart_count;
            showMsg('Item removed from cart', 'success');

            // If cart is now empty reload so empty message shows
            if(data.items.length === 0){
                setTimeout(function(){ location.reload(); }, 1000);
            }
        } else {
            showMsg(data.message, 'error');
        }
    });
}


function sendAjax(action, params, callback){
    var formData = new FormData();
    for(var key in params){
        formData.append(key, params[key]);
    }

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../api/cart.php?action=' + action, true);
    xhr.onload = function(){
        if(xhr.status === 200){
            var data = JSON.parse(xhr.responseText);
            callback(data);
        } else {
            showMsg('Server error. Please try again.', 'error');
        }
    };
    xhr.onerror = function(){
        showMsg('Network error. Please try again.', 'error');
    };
    xhr.send(formData);
}

// Show message at top of page for 3 seconds
function showMsg(msg, type){
    var el       = document.getElementById('ajax-msg');
    el.textContent = msg;
    el.className   = type;
    setTimeout(function(){
        el.textContent = '';
        el.className   = '';
    }, 3000);
}
</script>



</body>
</html>
