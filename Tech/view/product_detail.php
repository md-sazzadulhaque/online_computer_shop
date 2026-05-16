<?php
session_start();
require_once('../model/productModel.php');
require_once('../model/cartModel.php');

$id      = isset($_GET['id']) ? $_GET['id'] : '';
$product = getProductById($id);

if(!$product){
    header('location: home.php?error=Product not found');
    exit;
}

$cartCount = 0;
if(isset($_SESSION['customer_id'])){
    $cartCount = getCartCount($_SESSION['customer_id']);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $product['name']; ?> - TechShop</title>
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
            max-width: 750px;
            margin: 30px auto;
            background: white;
            border: 1px solid #ddd;
            padding: 30px;
        }

        h2 { font-size: 20px; margin-bottom: 20px; }

        .success {
            padding: 8px 12px; background: #e6ffe6;
            border: 1px solid green; color: green;
            margin-bottom: 14px; font-size: 14px;
        }
        .error {
            padding: 8px 12px; background: #ffe6e6;
            border: 1px solid red; color: red;
            margin-bottom: 14px; font-size: 14px;
        }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table td { padding: 10px 12px; border: 1px solid #eee; font-size: 14px; }
        table td:first-child { background-color: #f5f5f5; font-weight: bold; width: 170px; }

        .mfr-review-box {
            background-color: #fffbe6;
            border: 1px solid #f0d060;
            padding: 10px 14px;
            font-size: 13px;
            color: #666;
            font-style: italic;
            margin-bottom: 20px;
            border-radius: 3px;
        }
        .mfr-review-box strong { color: #888; font-style: normal; }

        .price-big { font-size: 24px; font-weight: bold; }
        .stock-ok  { color: green; font-weight: bold; }
        .stock-out { color: red;   font-weight: bold; }

        .qty-row { display: flex; align-items: center; gap: 10px; margin-top: 16px; }
        .qty-row label { font-size: 14px; font-weight: bold; }
        .qty-row input[type="number"] {
            padding: 7px 10px; border: 1px solid #ccc;
            width: 70px; font-size: 14px;
        }
        .btn-add-cart {
            padding: 8px 22px;
            background-color: #e67e00;
            color: white; border: none;
            cursor: pointer; font-size: 14px;
            font-weight: bold;
        }
        .btn-add-cart:hover { background-color: #c56a00; }

        .btn-back {
            display: inline-block;
            margin-top: 20px;
            padding: 7px 16px;
            background-color: #bbb;
            color: white; font-size: 13px;
        }
        .btn-back:hover { background-color: #999; }
    </style>
</head>
<body>

<div class="navbar">
    <span class="logo">TechShop</span>
    <a href="home.php">Home</a>
    <a href="cart.php" class="cart-link">
        Cart (<span id="nav-cart-count"><?php echo $cartCount; ?></span>)
    </a>
    <div class="nav-right">
        <?php if(isset($_SESSION['customer_id'])): ?>
            Welcome, <b style="color:white"><?php echo $_SESSION['customer_name']; ?></b>
            &nbsp;|&nbsp;
            <a href="../controller/authController.php?logout=true">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </div>
</div>

<div class="container">

    <div id="ajax-msg"></div>

    <h2><?php echo $product['name']; ?></h2>

    <!-- Manufacturer Review highlighted box -->
    <?php if($product['manufacturer_review']): ?>
    <div class="mfr-review-box">
        <strong>Manufacturer Review:</strong> <?php echo $product['manufacturer_review']; ?>
    </div>
    <?php endif; ?>

    <table>
        <tr>
            <td>Category</td>
            <td><?php echo $product['category_name']; ?></td>
        </tr>
        <tr>
            <td>Subcategory</td>
            <td><?php echo $product['subcategory_name'] ? $product['subcategory_name'] : '-'; ?></td>
        </tr>
        <tr>
            <td>Brand</td>
            <td><?php echo $product['brand_name'] ? $product['brand_name'] : '-'; ?></td>
        </tr>
        <tr>
            <td>Description</td>
            <td><?php echo $product['description']; ?></td>
        </tr>
        <tr>
            <td>Price</td>
            <td><span class="price-big">Tk <?php echo number_format($product['price'], 2); ?></span></td>
        </tr>
        <tr>
            <td>Stock</td>
            <td>
                <?php if($product['stock'] > 0): ?>
                    <span class="stock-ok"><?php echo $product['stock']; ?> units available</span>
                <?php else: ?>
                    <span class="stock-out">Out of Stock</span>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <!-- AJAX Add to Cart -->
    <?php if($product['stock'] > 0): ?>
        <div class="qty-row">
            <label>Quantity:</label>
            <input type="number" id="qty-input" value="1" min="1" max="<?php echo $product['stock']; ?>">
            <button class="btn-add-cart" onclick="addToCart(<?php echo $product['id']; ?>, <?php echo $product['stock']; ?>)">
                Add to Cart
            </button>
        </div>
    <?php else: ?>
        <p style="color:red; font-weight:bold; margin-top:10px;">This product is out of stock.</p>
    <?php endif; ?>

    <br>
    <a class="btn-back" href="home.php">Back to Home</a>
</div>

<script>
function addToCart(product_id, stock){
    <?php if(!isset($_SESSION['customer_id'])): ?>
        document.getElementById('ajax-msg').className = 'error';
        document.getElementById('ajax-msg').textContent = 'Please login to add items to cart';
        document.getElementById('ajax-msg').style.padding = '8px 12px';
        document.getElementById('ajax-msg').style.marginBottom = '14px';
        return;
    <?php endif; ?>

    var qty = parseInt(document.getElementById('qty-input').value);

    // JS validation
    if(isNaN(qty) || qty <= 0 || !Number.isInteger(qty)){
        showMsg('Quantity must be a positive number', 'error');
        return;
    }
    if(qty > stock){
        showMsg('Quantity cannot exceed stock (' + stock + ')', 'error');
        return;
    }

    var formData = new FormData();
    formData.append('product_id', product_id);
    formData.append('quantity',   qty);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../api/cart.php?action=add', true);
    xhr.onload = function(){
        var data = JSON.parse(xhr.responseText);
        if(data.status === 'success'){
            showMsg(data.message, 'success');
            document.getElementById('nav-cart-count').textContent = data.cart_count;
        } else {
            showMsg(data.message, 'error');
        }
    };
    xhr.send(formData);
}

function showMsg(msg, type){
    var el = document.getElementById('ajax-msg');
    el.textContent = msg;
    el.className   = type;
    el.style.padding     = '8px 12px';
    el.style.marginBottom = '14px';
    el.style.border = type === 'success' ? '1px solid green' : '1px solid red';
    el.style.backgroundColor = type === 'success' ? '#e6ffe6' : '#ffe6e6';
    setTimeout(function(){ el.textContent = ''; el.style.padding = '0'; el.style.border = 'none'; el.style.backgroundColor = ''; }, 3000);
}
</script>

</body>
</html>
