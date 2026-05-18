<?php

session_start();

require_once(__DIR__ . '/../model/productModel.php');
require_once(__DIR__ . '/../model/cartModel.php');

$id      = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = getProductById($id);

if(!$product){
    header('Location: home.php?error=Product not found');
    exit;
}

$cartCount = 0;
if(isset($_SESSION['user_id'])){
    $cartCount = getCartCount($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($product['name']); ?> - TechShop</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f0f0f0; color: #222; }
        a { text-decoration: none; color: #222; }

        .navbar {
            background: #222; padding: 12px 24px;
            display: flex; align-items: center; gap: 20px;
        }
        .navbar .logo { color: white; font-size: 20px; font-weight: bold; }
        .navbar a { color: #ccc; font-size: 14px; }
        .navbar a:hover { color: white; }
        .navbar .cart-link {
            background: #e67e00; color: white !important;
            padding: 5px 14px; border-radius: 3px; font-weight: bold;
        }
        .navbar .nav-right {
            margin-left: auto; display: flex;
            align-items: center; gap: 14px; color: #ccc; font-size: 14px;
        }
        .navbar .nav-right a { color: #ccc; }
        .navbar .nav-right a:hover { color: white; }

        .container {
            max-width: 800px;
            margin: 30px auto;
            background: white;
            border: 1px solid #ddd;
            padding: 30px;
        }

        h2 { font-size: 22px; margin-bottom: 16px; }

        #ajax-msg { margin-bottom: 12px; font-size: 14px; }
        .alert-success { padding: 8px 12px; background: #e6ffe6; border: 1px solid green; color: green; }
        .alert-error   { padding: 8px 12px; background: #ffe6e6; border: 1px solid red;   color: red;   }

        .mfr-box {
            background: #fffbe6;
            border: 1px solid #f0d060;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #666;
            font-style: italic;
            border-radius: 3px;
        }
        .mfr-box strong { color: #888; font-style: normal; font-size: 12px; text-transform: uppercase; }

        .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .detail-table td { padding: 10px 12px; border: 1px solid #eee; font-size: 14px; }
        .detail-table td:first-child {
            background: #f5f5f5; font-weight: bold; width: 160px;
        }

        .product-image {
            width: 100%;
            max-height: 250px;
            object-fit: contain;
            border: 1px solid #eee;
            margin-bottom: 20px;
            background: #f9f9f9;
            padding: 10px;
        }
        .no-image {
            width: 100%; height: 200px;
            background: #f5f5f5;
            border: 1px solid #eee;
            display: flex; align-items: center; justify-content: center;
            color: #bbb; font-size: 14px;
            margin-bottom: 20px;
        }

        .price-big  { font-size: 26px; font-weight: bold; }
        .stock-ok   { color: green; font-weight: bold; }
        .stock-out  { color: red;   font-weight: bold; }

        .qty-row { display: flex; align-items: center; gap: 10px; margin-top: 16px; }
        .qty-row label { font-size: 14px; font-weight: bold; }
        .qty-row input[type="number"] {
            padding: 7px 10px; border: 1px solid #ccc;
            width: 75px; font-size: 14px;
        }
        .btn-add-cart {
            padding: 8px 24px;
            background: #e67e00; color: white;
            border: none; cursor: pointer;
            font-size: 14px; font-weight: bold;
        }
        .btn-add-cart:hover { background: #c56a00; }

        .btn-back {
            display: inline-block; margin-top: 20px;
            padding: 8px 16px; background: #bbb; color: white; font-size: 13px;
        }
        .btn-back:hover { background: #999; }

        #qty-error { color: red; font-size: 13px; margin-top: 6px; display: none; }
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
        <?php if(isset($_SESSION['user_id'])): ?>
            Hi, <b style="color:white"><?php echo htmlspecialchars($_SESSION['user_name']); ?></b>
            &nbsp;|&nbsp;
            <a href="../controller/authController.php?logout=1">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </div>
</div>

<div class="container">

    <div id="ajax-msg"></div>

    <h2><?php echo htmlspecialchars($product['name']); ?></h2>

    <?php if($product['image_path'] && file_exists('../public/uploads/products/' . $product['image_path'])): ?>
        <img class="product-image"
             src="../public/uploads/products/<?php echo htmlspecialchars($product['image_path']); ?>"
             alt="<?php echo htmlspecialchars($product['name']); ?>">
    <?php else: ?>
        <div class="no-image">No image available</div>
    <?php endif; ?>

    <?php if($product['manufacturer_review']): ?>
    <div class="mfr-box">
        <strong>Manufacturer Review:</strong><br>
        <?php echo htmlspecialchars($product['manufacturer_review']); ?>
    </div>
    <?php endif; ?>

    <table class="detail-table">
        <tr>
            <td>Category</td>
            <td>
                <?php
                if($product['parent_name']){
                    echo htmlspecialchars($product['parent_name']) . ' &rsaquo; ' . htmlspecialchars($product['category_name']);
                } else {
                    echo htmlspecialchars($product['category_name']);
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>Brand</td>
            <td><?php echo $product['brand_name'] ? htmlspecialchars($product['brand_name']) : '-'; ?></td>
        </tr>
        <tr>
            <td>Description</td>
            <td><?php echo nl2br(htmlspecialchars($product['description'])); ?></td>
        </tr>
        <tr>
            <td>Price</td>
            <td><span class="price-big">Tk <?php echo number_format($product['price'], 2); ?></span></td>
        </tr>
        <tr>
            <td>Stock Status</td>
            <td>
                <?php if($product['stock'] > 0): ?>
                    <span class="stock-ok"><?php echo (int)$product['stock']; ?> units in stock</span>
                <?php else: ?>
                    <span class="stock-out">Out of Stock</span>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <?php if($product['stock'] > 0): ?>
        <div class="qty-row">
            <label>Quantity:</label>
            <input type="number" id="qty-input" value="1" min="1"
                   max="<?php echo (int)$product['stock']; ?>">
            <button class="btn-add-cart"
                    onclick="addToCart(<?php echo $product['id']; ?>, <?php echo (int)$product['stock']; ?>)">
                Add to Cart
            </button>
        </div>
        <p id="qty-error"></p>
    <?php else: ?>
        <p style="color:red; font-weight:bold; margin-top:12px;">This product is out of stock.</p>
    <?php endif; ?>

    <a class="btn-back" href="home.php">&larr; Back to Home</a>

</div>

<script>
function addToCart(product_id, stock){
    <?php if(!isset($_SESSION['user_id'])): ?>
        showMsg('Please login to add items to cart', 'error');
        return;
    <?php endif; ?>

    var qtyEl = document.getElementById('qty-input');
    var errEl = document.getElementById('qty-error');
    var qty   = parseInt(qtyEl.value);

    errEl.style.display = 'none';
    errEl.textContent   = '';

    if(isNaN(qty) || qty <= 0 || !Number.isInteger(qty)){
        errEl.textContent   = 'Quantity must be a positive whole number';
        errEl.style.display = 'block';
        return;
    }
    if(qty > stock){
        errEl.textContent   = 'Quantity cannot exceed available stock (' + stock + ')';
        errEl.style.display = 'block';
        return;
    }

    var fd = new FormData();
    fd.append('product_id', product_id);
    fd.append('quantity', qty);

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
    xhr.send(fd);
}

function showMsg(msg, type){
    var el = document.getElementById('ajax-msg');
    el.textContent = msg;
    el.className   = 'alert-' + type;
    setTimeout(function(){ el.textContent = ''; el.className = ''; }, 3000);
}
</script>

</body>
</html>
<?php

session_start();

require_once(__DIR__ . '/../model/productModel.php');
require_once(__DIR__ . '/../model/cartModel.php');

$id      = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = getProductById($id);

if(!$product){
    header('Location: home.php?error=Product not found');
    exit;
}

$cartCount = 0;
if(isset($_SESSION['user_id'])){
    $cartCount = getCartCount($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($product['name']); ?> - TechShop</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f0f0f0; color: #222; }
        a { text-decoration: none; color: #222; }

        .navbar {
            background: #222; padding: 12px 24px;
            display: flex; align-items: center; gap: 20px;
        }
        .navbar .logo { color: white; font-size: 20px; font-weight: bold; }
        .navbar a { color: #ccc; font-size: 14px; }
        .navbar a:hover { color: white; }
        .navbar .cart-link {
            background: #e67e00; color: white !important;
            padding: 5px 14px; border-radius: 3px; font-weight: bold;
        }
        .navbar .nav-right {
            margin-left: auto; display: flex;
            align-items: center; gap: 14px; color: #ccc; font-size: 14px;
        }
        .navbar .nav-right a { color: #ccc; }
        .navbar .nav-right a:hover { color: white; }

        .container {
            max-width: 800px;
            margin: 30px auto;
            background: white;
            border: 1px solid #ddd;
            padding: 30px;
        }

        h2 { font-size: 22px; margin-bottom: 16px; }

        #ajax-msg { margin-bottom: 12px; font-size: 14px; }
        .alert-success { padding: 8px 12px; background: #e6ffe6; border: 1px solid green; color: green; }
        .alert-error   { padding: 8px 12px; background: #ffe6e6; border: 1px solid red;   color: red;   }

        .mfr-box {
            background: #fffbe6;
            border: 1px solid #f0d060;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #666;
            font-style: italic;
            border-radius: 3px;
        }
        .mfr-box strong { color: #888; font-style: normal; font-size: 12px; text-transform: uppercase; }

        .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .detail-table td { padding: 10px 12px; border: 1px solid #eee; font-size: 14px; }
        .detail-table td:first-child {
            background: #f5f5f5; font-weight: bold; width: 160px;
        }

        .product-image {
            width: 100%;
            max-height: 250px;
            object-fit: contain;
            border: 1px solid #eee;
            margin-bottom: 20px;
            background: #f9f9f9;
            padding: 10px;
        }
        .no-image {
            width: 100%; height: 200px;
            background: #f5f5f5;
            border: 1px solid #eee;
            display: flex; align-items: center; justify-content: center;
            color: #bbb; font-size: 14px;
            margin-bottom: 20px;
        }

        .price-big  { font-size: 26px; font-weight: bold; }
        .stock-ok   { color: green; font-weight: bold; }
        .stock-out  { color: red;   font-weight: bold; }

        .qty-row { display: flex; align-items: center; gap: 10px; margin-top: 16px; }
        .qty-row label { font-size: 14px; font-weight: bold; }
        .qty-row input[type="number"] {
            padding: 7px 10px; border: 1px solid #ccc;
            width: 75px; font-size: 14px;
        }
        .btn-add-cart {
            padding: 8px 24px;
            background: #e67e00; color: white;
            border: none; cursor: pointer;
            font-size: 14px; font-weight: bold;
        }
        .btn-add-cart:hover { background: #c56a00; }

        .btn-back {
            display: inline-block; margin-top: 20px;
            padding: 8px 16px; background: #bbb; color: white; font-size: 13px;
        }
        .btn-back:hover { background: #999; }

        #qty-error { color: red; font-size: 13px; margin-top: 6px; display: none; }
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
        <?php if(isset($_SESSION['user_id'])): ?>
            Hi, <b style="color:white"><?php echo htmlspecialchars($_SESSION['user_name']); ?></b>
            &nbsp;|&nbsp;
            <a href="../controller/authController.php?logout=1">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </div>
</div>

<div class="container">

    <div id="ajax-msg"></div>

    <h2><?php echo htmlspecialchars($product['name']); ?></h2>

    <?php if($product['image_path'] && file_exists('../public/uploads/products/' . $product['image_path'])): ?>
        <img class="product-image"
             src="../public/uploads/products/<?php echo htmlspecialchars($product['image_path']); ?>"
             alt="<?php echo htmlspecialchars($product['name']); ?>">
    <?php else: ?>
        <div class="no-image">No image available</div>
    <?php endif; ?>

    <?php if($product['manufacturer_review']): ?>
    <div class="mfr-box">
        <strong>Manufacturer Review:</strong><br>
        <?php echo htmlspecialchars($product['manufacturer_review']); ?>
    </div>
    <?php endif; ?>

    <table class="detail-table">
        <tr>
            <td>Category</td>
            <td>
                <?php
                if($product['parent_name']){
                    echo htmlspecialchars($product['parent_name']) . ' &rsaquo; ' . htmlspecialchars($product['category_name']);
                } else {
                    echo htmlspecialchars($product['category_name']);
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>Brand</td>
            <td><?php echo $product['brand_name'] ? htmlspecialchars($product['brand_name']) : '-'; ?></td>
        </tr>
        <tr>
            <td>Description</td>
            <td><?php echo nl2br(htmlspecialchars($product['description'])); ?></td>
        </tr>
        <tr>
            <td>Price</td>
            <td><span class="price-big">Tk <?php echo number_format($product['price'], 2); ?></span></td>
        </tr>
        <tr>
            <td>Stock Status</td>
            <td>
                <?php if($product['stock'] > 0): ?>
                    <span class="stock-ok"><?php echo (int)$product['stock']; ?> units in stock</span>
                <?php else: ?>
                    <span class="stock-out">Out of Stock</span>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <?php if($product['stock'] > 0): ?>
        <div class="qty-row">
            <label>Quantity:</label>
            <input type="number" id="qty-input" value="1" min="1"
                   max="<?php echo (int)$product['stock']; ?>">
            <button class="btn-add-cart"
                    onclick="addToCart(<?php echo $product['id']; ?>, <?php echo (int)$product['stock']; ?>)">
                Add to Cart
            </button>
        </div>
        <p id="qty-error"></p>
    <?php else: ?>
        <p style="color:red; font-weight:bold; margin-top:12px;">This product is out of stock.</p>
    <?php endif; ?>

    <a class="btn-back" href="home.php">&larr; Back to Home</a>

</div>

<script>
function addToCart(product_id, stock){
    <?php if(!isset($_SESSION['user_id'])): ?>
        showMsg('Please login to add items to cart', 'error');
        return;
    <?php endif; ?>

    var qtyEl = document.getElementById('qty-input');
    var errEl = document.getElementById('qty-error');
    var qty   = parseInt(qtyEl.value);

    errEl.style.display = 'none';
    errEl.textContent   = '';

    if(isNaN(qty) || qty <= 0 || !Number.isInteger(qty)){
        errEl.textContent   = 'Quantity must be a positive whole number';
        errEl.style.display = 'block';
        return;
    }
    if(qty > stock){
        errEl.textContent   = 'Quantity cannot exceed available stock (' + stock + ')';
        errEl.style.display = 'block';
        return;
    }

    var fd = new FormData();
    fd.append('product_id', product_id);
    fd.append('quantity', qty);

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
    xhr.send(fd);
}

function showMsg(msg, type){
    var el = document.getElementById('ajax-msg');
    el.textContent = msg;
    el.className   = 'alert-' + type;
    setTimeout(function(){ el.textContent = ''; el.className = ''; }, 3000);
}
</script>

</body>
</html>
