<?php

session_start();

require_once(__DIR__ . '/../model/productModel.php');
require_once(__DIR__ . '/../model/cartModel.php');

$topCategories   = getTopCategories();
$allSubcategories = getAllSubcategories();
$allBrands       = getAllBrands();

$cartCount = 0;
if(isset($_SESSION['user_id'])){
    $cartCount = getCartCount($_SESSION['user_id']);
}

$initProducts = getAllProducts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop - Browse</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f0f0f0; color: #222; }
        a { text-decoration: none; color: #222; }


        .navbar {
            background: #222;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .navbar .logo { color: white; font-size: 20px; font-weight: bold; }
        .navbar a { color: #ccc; font-size: 14px; }
        .navbar a:hover { color: white; }
        .navbar .cart-link {
            background: #e67e00;
            color: white !important;
            padding: 5px 14px;
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

        /*layout*/
        .wrapper {
            display: flex;
            max-width: 1200px;
            margin: 20px auto;
            gap: 16px;
            align-items: flex-start;
        }

        .sidebar {
            width: 210px;
            flex-shrink: 0;
            background: white;
            border: 1px solid #ddd;
            padding: 14px;
        }
        .sidebar h4 {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #aaa;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 8px;
            margin-top: 16px;
        }
        .sidebar h4:first-child { margin-top: 0; }
        .sidebar a {
            display: block;
            padding: 5px 8px;
            font-size: 13px;
            color: #444;
            border-radius: 3px;
        }
        .sidebar a:hover { background: #f5f5f5; }
        .sidebar a.active { background: #222; color: white; }
        .sub-label { font-size: 11px; color: #bbb; margin-left: 4px; }

        .main { flex: 1; }

        .search-box {
            background: white;
            border: 1px solid #ddd;
            padding: 12px 14px;
            display: flex;
            gap: 8px;
            margin-bottom: 10px;
        }
        .search-box input[type="text"] {
            flex: 1;
            padding: 8px 10px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        .search-box button {
            padding: 8px 20px;
            background: #222;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .search-box button:hover { background: #444; }

        .filter-box {
            background: white;
            border: 1px solid #ddd;
            padding: 12px 14px;
            margin-bottom: 10px;
        }
        .filter-title { font-size: 13px; font-weight: bold; color: #555; margin-bottom: 10px; }
        .filter-row   { display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-end; }
        .filter-group { display: flex; flex-direction: column; gap: 4px; }
        .filter-group label { font-size: 12px; color: #777; font-weight: bold; }
        .filter-group input[type="number"],
        .filter-group select {
            padding: 7px 8px;
            border: 1px solid #ccc;
            font-size: 13px;
            width: 135px;
        }
        .btn-filter {
            padding: 7px 18px;
            background: #222;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 13px;
        }
        .btn-filter:hover { background: #444; }
        .btn-clear {
            padding: 7px 14px;
            background: #bbb;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 13px;
        }
        .btn-clear:hover { background: #999; }
        #filter-error { color: red; font-size: 13px; margin-top: 8px; display: none; }

        .alert-success {
            padding: 8px 12px; background: #e6ffe6;
            border: 1px solid green; color: green;
            margin-bottom: 10px; font-size: 14px;
        }
        .alert-error {
            padding: 8px 12px; background: #ffe6e6;
            border: 1px solid red; color: red;
            margin-bottom: 10px; font-size: 14px;
        }

        .result-info { font-size: 13px; color: #666; margin-bottom: 8px; }

        #product-wrap table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border: 1px solid #ddd;
        }
        #product-wrap th {
            background: #222;
            color: white;
            padding: 10px 12px;
            text-align: left;
            font-size: 13px;
        }
        #product-wrap td {
            padding: 10px 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            vertical-align: top;
        }
        #product-wrap tr:hover td { background: #fafafa; }

        .mfr-review { font-size: 12px; color: #888; font-style: italic; margin-top: 3px; }
        .price-col  { font-weight: bold; }
        .stock-ok   { color: green; font-size: 13px; }
        .stock-out  { color: red;   font-size: 13px; }

        .btn-view {
            display: inline-block;
            padding: 5px 12px;
            background: #444;
            color: white;
            font-size: 12px;
        }
        .btn-view:hover { background: #222; }
        .btn-add {
            padding: 5px 12px;
            background: #e67e00;
            color: white;
            font-size: 12px;
            border: none;
            cursor: pointer;
            margin-left: 4px;
        }
        .btn-add:hover { background: #c56a00; }
        .btn-add:disabled { background: #ccc; cursor: not-allowed; }

        .no-products {
            background: white;
            border: 1px solid #ddd;
            padding: 30px;
            text-align: center;
            color: #888;
        }
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

<div class="wrapper">

    <div class="sidebar">

        <h4>Categories</h4>
        <a href="#" onclick="sidebarFilter('','','');return false;"
           id="link-all" class="active">All Products</a>

        <?php foreach($topCategories as $cat): ?>
            <a href="#" onclick="sidebarFilter('<?php echo $cat['id']; ?>','','');return false;"
               id="link-cat-<?php echo $cat['id']; ?>">
                <?php echo htmlspecialchars($cat['name']); ?>
            </a>
        <?php endforeach; ?>

        <h4>Subcategories</h4>
        <?php foreach($allSubcategories as $sub): ?>
            <a href="#" onclick="sidebarFilter('','<?php echo $sub['id']; ?>','');return false;"
               id="link-sub-<?php echo $sub['id']; ?>">
                <?php echo htmlspecialchars($sub['name']); ?>
                <span class="sub-label">(<?php echo htmlspecialchars($sub['parent_name']); ?>)</span>
            </a>
        <?php endforeach; ?>

        <h4>Brands</h4>
        <?php foreach($allBrands as $brand): ?>
            <a href="#" onclick="sidebarFilter('','','<?php echo $brand['id']; ?>');return false;"
               id="link-brand-<?php echo $brand['id']; ?>">
                <?php echo htmlspecialchars($brand['name']); ?>
            </a>
        <?php endforeach; ?>

    </div>

    <div class="main">

        <div id="ajax-msg"></div>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>
        <?php if(isset($_GET['error'])): ?>
            <div class="alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <div class="search-box">
            <input type="text" id="search-input" placeholder="Search products, brands, categories...">
            <button onclick="doSearch()">Search</button>
        </div>

        <div class="filter-box">
            <div class="filter-title">Filter Products</div>
            <div class="filter-row">

                <div class="filter-group">
                    <label>Min Price (Tk)</label>
                    <input type="number" id="filter-min" placeholder="0" min="0">
                </div>

                <div class="filter-group">
                    <label>Max Price (Tk)</label>
                    <input type="number" id="filter-max" placeholder="Any" min="0">
                </div>

                <div class="filter-group">
                    <label>Category</label>
                    <select id="filter-cat">
                        <option value="">All Categories</option>
                        <?php foreach($topCategories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Brand</label>
                    <select id="filter-brand">
                        <option value="">All Brands</option>
                        <?php foreach($allBrands as $brand): ?>
                            <option value="<?php echo $brand['id']; ?>">
                                <?php echo htmlspecialchars($brand['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button class="btn-filter" onclick="doFilter()">Apply Filter</button>
                <button class="btn-clear"  onclick="clearAll()">Clear</button>

            </div>
            <p id="filter-error"></p>
        </div>

        <p class="result-info" id="result-info">
            Showing <?php echo count($initProducts); ?> product(s)
        </p>

        <div id="product-wrap">
            <?php if(empty($initProducts)): ?>
                <div class="no-products">No products found.</div>
            <?php else: ?>
            <table>
                <tr>
                    <th>Product Name &amp; Manufacturer Review</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Action</th>
                </tr>
                <?php foreach($initProducts as $p): ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($p['name']); ?></strong>
                        <?php if($p['manufacturer_review']): ?>
                            <div class="mfr-review">
                                <?php echo htmlspecialchars($p['manufacturer_review']); ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        if($p['parent_name']){
                            echo htmlspecialchars($p['parent_name']) . ' &rsaquo; ' . htmlspecialchars($p['category_name']);
                        } else {
                            echo htmlspecialchars($p['category_name']);
                        }
                        ?>
                    </td>
                    <td><?php echo $p['brand_name'] ? htmlspecialchars($p['brand_name']) : '-'; ?></td>
                    <td class="price-col">Tk <?php echo number_format($p['price'], 2); ?></td>
                    <td>
                        <?php if($p['stock'] > 0): ?>
                            <span class="stock-ok"><?php echo (int)$p['stock']; ?> left</span>
                        <?php else: ?>
                            <span class="stock-out">Out of stock</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a class="btn-view" href="product_detail.php?id=<?php echo $p['id']; ?>">View</a>
                        <?php if($p['stock'] > 0): ?>
                            <button class="btn-add" onclick="addToCart(<?php echo $p['id']; ?>)">+ Cart</button>
                        <?php else: ?>
                            <button class="btn-add" disabled>+ Cart</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php endif; ?>
        </div>

    </div>
</div>
<script>
var searchTimer = null;

document.getElementById('search-input').addEventListener('keyup', function(){
    clearTimeout(searchTimer);
    searchTimer = setTimeout(doSearch, 400);
});

function doSearch(){
    var q   = document.getElementById('search-input').value;
    var min = document.getElementById('filter-min').value;
    var max = document.getElementById('filter-max').value;
    var cat = document.getElementById('filter-cat').value;
    var brd = document.getElementById('filter-brand').value;
    callAPI(q, min, max, cat, brd);
}

function doFilter(){
    var errEl = document.getElementById('filter-error');
    errEl.style.display = 'none';
    errEl.textContent   = '';

    var min = document.getElementById('filter-min').value;
    var max = document.getElementById('filter-max').value;

    if(min !== '' && isNaN(parseFloat(min))){
        errEl.textContent   = 'Min price must be a number';
        errEl.style.display = 'block';
        return;
    }
    if(max !== '' && isNaN(parseFloat(max))){
        errEl.textContent   = 'Max price must be a number';
        errEl.style.display = 'block';
        return;
    }
    if(min !== '' && max !== '' && parseFloat(min) > parseFloat(max)){
        errEl.textContent   = 'Min price cannot be greater than max price';
        errEl.style.display = 'block';
        return;
    }

    doSearch();
}

function sidebarFilter(cat_id, sub_id, brand_id){
    document.getElementById('search-input').value = '';
    document.getElementById('filter-min').value   = '';
    document.getElementById('filter-max').value   = '';
    document.getElementById('filter-brand').value = brand_id;
    document.getElementById('filter-cat').value   = cat_id;

    markActive(cat_id, sub_id, brand_id);

    var effective_cat = sub_id !== '' ? sub_id : cat_id;
    callAPI('', '', '', effective_cat, brand_id);
}

function markActive(cat_id, sub_id, brand_id){
    document.querySelectorAll('.sidebar a').forEach(function(a){
        a.classList.remove('active');
    });
    if(cat_id === '' && sub_id === '' && brand_id === ''){
        document.getElementById('link-all').classList.add('active');
    }
    if(cat_id   !== ''){ var e = document.getElementById('link-cat-'   + cat_id);   if(e) e.classList.add('active'); }
    if(sub_id   !== ''){ var e = document.getElementById('link-sub-'   + sub_id);   if(e) e.classList.add('active'); }
    if(brand_id !== ''){ var e = document.getElementById('link-brand-' + brand_id); if(e) e.classList.add('active'); }
}

function clearAll(){
    document.getElementById('search-input').value = '';
    document.getElementById('filter-min').value   = '';
    document.getElementById('filter-max').value   = '';
    document.getElementById('filter-cat').value   = '';
    document.getElementById('filter-brand').value = '';
    document.getElementById('filter-error').style.display = 'none';
    markActive('','','');
    callAPI('','','','','');
}

function callAPI(q, min, max, cat, brd){
    var url = '../api/search.php'
            + '?q='           + encodeURIComponent(q)
            + '&min_price='   + encodeURIComponent(min)
            + '&max_price='   + encodeURIComponent(max)
            + '&category_id=' + encodeURIComponent(cat)
            + '&brand_id='    + encodeURIComponent(brd);

    document.getElementById('product-wrap').innerHTML =
        '<p style="padding:20px;color:#888;">Loading...</p>';

    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.onload = function(){
        if(xhr.status === 200){
            var data = JSON.parse(xhr.responseText);
            if(data.status === 'success'){
                renderTable(data.products);
                document.getElementById('result-info').textContent =
                    'Showing ' + data.count + ' product(s)';
            } else {
                document.getElementById('product-wrap').innerHTML =
                    '<div class="no-products">' + data.message + '</div>';
            }
        }
    };
    xhr.send();
}

function renderTable(products){
    if(products.length === 0){
        document.getElementById('product-wrap').innerHTML =
            '<div class="no-products">No products found.</div>';
        return;
    }

    var html = '<table>';
    html += '<tr>';
    html += '<th>Product Name &amp; Manufacturer Review</th>';
    html += '<th>Category</th>';
    html += '<th>Brand</th>';
    html += '<th>Price</th>';
    html += '<th>Stock</th>';
    html += '<th>Action</th>';
    html += '</tr>';

    for(var i = 0; i < products.length; i++){
        var p       = products[i];
        var inStock = parseInt(p.stock) > 0;
        var catLabel = p.parent_name
            ? p.parent_name + ' &rsaquo; ' + p.category_name
            : p.category_name;

        html += '<tr>';
        html += '<td><strong>' + escHtml(p.name) + '</strong>';
        if(p.manufacturer_review){
            html += '<div class="mfr-review">' + escHtml(p.manufacturer_review) + '</div>';
        }
        html += '</td>';
        html += '<td>' + catLabel + '</td>';
        html += '<td>' + (p.brand_name ? escHtml(p.brand_name) : '-') + '</td>';
        html += '<td class="price-col">Tk ' + parseFloat(p.price).toLocaleString() + '</td>';

        if(inStock){
            html += '<td><span class="stock-ok">' + p.stock + ' left</span></td>';
            html += '<td>'
                  + '<a class="btn-view" href="product_detail.php?id=' + p.id + '">View</a>'
                  + ' <button class="btn-add" onclick="addToCart(' + p.id + ')">+ Cart</button>'
                  + '</td>';
        } else {
            html += '<td><span class="stock-out">Out of stock</span></td>';
            html += '<td>'
                  + '<a class="btn-view" href="product_detail.php?id=' + p.id + '">View</a>'
                  + ' <button class="btn-add" disabled>+ Cart</button>'
                  + '</td>';
        }
        html += '</tr>';
    }
    html += '</table>';
    document.getElementById('product-wrap').innerHTML = html;
}

function addToCart(product_id){
    <?php if(!isset($_SESSION['user_id'])): ?>
        showMsg('Please login to add items to cart', 'error');
        return;
    <?php endif; ?>

    var fd = new FormData();
    fd.append('product_id', product_id);
    fd.append('quantity', 1);

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

function escHtml(str){
    var d = document.createElement('div');
    d.textContent = str || '';
    return d.innerHTML;
}

function showMsg(msg, type){
    var old = document.getElementById('ajax-msg');
    if(old) old.innerHTML = '';
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

$topCategories   = getTopCategories();
$allSubcategories = getAllSubcategories();
$allBrands       = getAllBrands();

$cartCount = 0;
if(isset($_SESSION['user_id'])){
    $cartCount = getCartCount($_SESSION['user_id']);
}

$initProducts = getAllProducts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop - Browse</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f0f0f0; color: #222; }
        a { text-decoration: none; color: #222; }


        .navbar {
            background: #222;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .navbar .logo { color: white; font-size: 20px; font-weight: bold; }
        .navbar a { color: #ccc; font-size: 14px; }
        .navbar a:hover { color: white; }
        .navbar .cart-link {
            background: #e67e00;
            color: white !important;
            padding: 5px 14px;
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

        /*layout*/
        .wrapper {
            display: flex;
            max-width: 1200px;
            margin: 20px auto;
            gap: 16px;
            align-items: flex-start;
        }

        .sidebar {
            width: 210px;
            flex-shrink: 0;
            background: white;
            border: 1px solid #ddd;
            padding: 14px;
        }
        .sidebar h4 {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #aaa;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 8px;
            margin-top: 16px;
        }
        .sidebar h4:first-child { margin-top: 0; }
        .sidebar a {
            display: block;
            padding: 5px 8px;
            font-size: 13px;
            color: #444;
            border-radius: 3px;
        }
        .sidebar a:hover { background: #f5f5f5; }
        .sidebar a.active { background: #222; color: white; }
        .sub-label { font-size: 11px; color: #bbb; margin-left: 4px; }

        .main { flex: 1; }

        .search-box {
            background: white;
            border: 1px solid #ddd;
            padding: 12px 14px;
            display: flex;
            gap: 8px;
            margin-bottom: 10px;
        }
        .search-box input[type="text"] {
            flex: 1;
            padding: 8px 10px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        .search-box button {
            padding: 8px 20px;
            background: #222;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .search-box button:hover { background: #444; }

        .filter-box {
            background: white;
            border: 1px solid #ddd;
            padding: 12px 14px;
            margin-bottom: 10px;
        }
        .filter-title { font-size: 13px; font-weight: bold; color: #555; margin-bottom: 10px; }
        .filter-row   { display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-end; }
        .filter-group { display: flex; flex-direction: column; gap: 4px; }
        .filter-group label { font-size: 12px; color: #777; font-weight: bold; }
        .filter-group input[type="number"],
        .filter-group select {
            padding: 7px 8px;
            border: 1px solid #ccc;
            font-size: 13px;
            width: 135px;
        }
        .btn-filter {
            padding: 7px 18px;
            background: #222;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 13px;
        }
        .btn-filter:hover { background: #444; }
        .btn-clear {
            padding: 7px 14px;
            background: #bbb;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 13px;
        }
        .btn-clear:hover { background: #999; }
        #filter-error { color: red; font-size: 13px; margin-top: 8px; display: none; }

        .alert-success {
            padding: 8px 12px; background: #e6ffe6;
            border: 1px solid green; color: green;
            margin-bottom: 10px; font-size: 14px;
        }
        .alert-error {
            padding: 8px 12px; background: #ffe6e6;
            border: 1px solid red; color: red;
            margin-bottom: 10px; font-size: 14px;
        }

        .result-info { font-size: 13px; color: #666; margin-bottom: 8px; }

        #product-wrap table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border: 1px solid #ddd;
        }
        #product-wrap th {
            background: #222;
            color: white;
            padding: 10px 12px;
            text-align: left;
            font-size: 13px;
        }
        #product-wrap td {
            padding: 10px 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            vertical-align: top;
        }
        #product-wrap tr:hover td { background: #fafafa; }

        .mfr-review { font-size: 12px; color: #888; font-style: italic; margin-top: 3px; }
        .price-col  { font-weight: bold; }
        .stock-ok   { color: green; font-size: 13px; }
        .stock-out  { color: red;   font-size: 13px; }

        .btn-view {
            display: inline-block;
            padding: 5px 12px;
            background: #444;
            color: white;
            font-size: 12px;
        }
        .btn-view:hover { background: #222; }
        .btn-add {
            padding: 5px 12px;
            background: #e67e00;
            color: white;
            font-size: 12px;
            border: none;
            cursor: pointer;
            margin-left: 4px;
        }
        .btn-add:hover { background: #c56a00; }
        .btn-add:disabled { background: #ccc; cursor: not-allowed; }

        .no-products {
            background: white;
            border: 1px solid #ddd;
            padding: 30px;
            text-align: center;
            color: #888;
        }
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

<div class="wrapper">

    <div class="sidebar">

        <h4>Categories</h4>
        <a href="#" onclick="sidebarFilter('','','');return false;"
           id="link-all" class="active">All Products</a>

        <?php foreach($topCategories as $cat): ?>
            <a href="#" onclick="sidebarFilter('<?php echo $cat['id']; ?>','','');return false;"
               id="link-cat-<?php echo $cat['id']; ?>">
                <?php echo htmlspecialchars($cat['name']); ?>
            </a>
        <?php endforeach; ?>

        <h4>Subcategories</h4>
        <?php foreach($allSubcategories as $sub): ?>
            <a href="#" onclick="sidebarFilter('','<?php echo $sub['id']; ?>','');return false;"
               id="link-sub-<?php echo $sub['id']; ?>">
                <?php echo htmlspecialchars($sub['name']); ?>
                <span class="sub-label">(<?php echo htmlspecialchars($sub['parent_name']); ?>)</span>
            </a>
        <?php endforeach; ?>

        <h4>Brands</h4>
        <?php foreach($allBrands as $brand): ?>
            <a href="#" onclick="sidebarFilter('','','<?php echo $brand['id']; ?>');return false;"
               id="link-brand-<?php echo $brand['id']; ?>">
                <?php echo htmlspecialchars($brand['name']); ?>
            </a>
        <?php endforeach; ?>

    </div>

    <div class="main">

        <div id="ajax-msg"></div>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>
        <?php if(isset($_GET['error'])): ?>
            <div class="alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <div class="search-box">
            <input type="text" id="search-input" placeholder="Search products, brands, categories...">
            <button onclick="doSearch()">Search</button>
        </div>

        <div class="filter-box">
            <div class="filter-title">Filter Products</div>
            <div class="filter-row">

                <div class="filter-group">
                    <label>Min Price (Tk)</label>
                    <input type="number" id="filter-min" placeholder="0" min="0">
                </div>

                <div class="filter-group">
                    <label>Max Price (Tk)</label>
                    <input type="number" id="filter-max" placeholder="Any" min="0">
                </div>

                <div class="filter-group">
                    <label>Category</label>
                    <select id="filter-cat">
                        <option value="">All Categories</option>
                        <?php foreach($topCategories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Brand</label>
                    <select id="filter-brand">
                        <option value="">All Brands</option>
                        <?php foreach($allBrands as $brand): ?>
                            <option value="<?php echo $brand['id']; ?>">
                                <?php echo htmlspecialchars($brand['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button class="btn-filter" onclick="doFilter()">Apply Filter</button>
                <button class="btn-clear"  onclick="clearAll()">Clear</button>

            </div>
            <p id="filter-error"></p>
        </div>

        <p class="result-info" id="result-info">
            Showing <?php echo count($initProducts); ?> product(s)
        </p>

        <div id="product-wrap">
            <?php if(empty($initProducts)): ?>
                <div class="no-products">No products found.</div>
            <?php else: ?>
            <table>
                <tr>
                    <th>Product Name &amp; Manufacturer Review</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Action</th>
                </tr>
                <?php foreach($initProducts as $p): ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($p['name']); ?></strong>
                        <?php if($p['manufacturer_review']): ?>
                            <div class="mfr-review">
                                <?php echo htmlspecialchars($p['manufacturer_review']); ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        if($p['parent_name']){
                            echo htmlspecialchars($p['parent_name']) . ' &rsaquo; ' . htmlspecialchars($p['category_name']);
                        } else {
                            echo htmlspecialchars($p['category_name']);
                        }
                        ?>
                    </td>
                    <td><?php echo $p['brand_name'] ? htmlspecialchars($p['brand_name']) : '-'; ?></td>
                    <td class="price-col">Tk <?php echo number_format($p['price'], 2); ?></td>
                    <td>
                        <?php if($p['stock'] > 0): ?>
                            <span class="stock-ok"><?php echo (int)$p['stock']; ?> left</span>
                        <?php else: ?>
                            <span class="stock-out">Out of stock</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a class="btn-view" href="product_detail.php?id=<?php echo $p['id']; ?>">View</a>
                        <?php if($p['stock'] > 0): ?>
                            <button class="btn-add" onclick="addToCart(<?php echo $p['id']; ?>)">+ Cart</button>
                        <?php else: ?>
                            <button class="btn-add" disabled>+ Cart</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php endif; ?>
        </div>

    </div>
</div>
<script>
var searchTimer = null;

document.getElementById('search-input').addEventListener('keyup', function(){
    clearTimeout(searchTimer);
    searchTimer = setTimeout(doSearch, 400);
});

function doSearch(){
    var q   = document.getElementById('search-input').value;
    var min = document.getElementById('filter-min').value;
    var max = document.getElementById('filter-max').value;
    var cat = document.getElementById('filter-cat').value;
    var brd = document.getElementById('filter-brand').value;
    callAPI(q, min, max, cat, brd);
}

function doFilter(){
    var errEl = document.getElementById('filter-error');
    errEl.style.display = 'none';
    errEl.textContent   = '';

    var min = document.getElementById('filter-min').value;
    var max = document.getElementById('filter-max').value;

    if(min !== '' && isNaN(parseFloat(min))){
        errEl.textContent   = 'Min price must be a number';
        errEl.style.display = 'block';
        return;
    }
    if(max !== '' && isNaN(parseFloat(max))){
        errEl.textContent   = 'Max price must be a number';
        errEl.style.display = 'block';
        return;
    }
    if(min !== '' && max !== '' && parseFloat(min) > parseFloat(max)){
        errEl.textContent   = 'Min price cannot be greater than max price';
        errEl.style.display = 'block';
        return;
    }

    doSearch();
}

function sidebarFilter(cat_id, sub_id, brand_id){
    document.getElementById('search-input').value = '';
    document.getElementById('filter-min').value   = '';
    document.getElementById('filter-max').value   = '';
    document.getElementById('filter-brand').value = brand_id;
    document.getElementById('filter-cat').value   = cat_id;

    markActive(cat_id, sub_id, brand_id);

    var effective_cat = sub_id !== '' ? sub_id : cat_id;
    callAPI('', '', '', effective_cat, brand_id);
}

function markActive(cat_id, sub_id, brand_id){
    document.querySelectorAll('.sidebar a').forEach(function(a){
        a.classList.remove('active');
    });
    if(cat_id === '' && sub_id === '' && brand_id === ''){
        document.getElementById('link-all').classList.add('active');
    }
    if(cat_id   !== ''){ var e = document.getElementById('link-cat-'   + cat_id);   if(e) e.classList.add('active'); }
    if(sub_id   !== ''){ var e = document.getElementById('link-sub-'   + sub_id);   if(e) e.classList.add('active'); }
    if(brand_id !== ''){ var e = document.getElementById('link-brand-' + brand_id); if(e) e.classList.add('active'); }
}

function clearAll(){
    document.getElementById('search-input').value = '';
    document.getElementById('filter-min').value   = '';
    document.getElementById('filter-max').value   = '';
    document.getElementById('filter-cat').value   = '';
    document.getElementById('filter-brand').value = '';
    document.getElementById('filter-error').style.display = 'none';
    markActive('','','');
    callAPI('','','','','');
}

function callAPI(q, min, max, cat, brd){
    var url = '../api/search.php'
            + '?q='           + encodeURIComponent(q)
            + '&min_price='   + encodeURIComponent(min)
            + '&max_price='   + encodeURIComponent(max)
            + '&category_id=' + encodeURIComponent(cat)
            + '&brand_id='    + encodeURIComponent(brd);

    document.getElementById('product-wrap').innerHTML =
        '<p style="padding:20px;color:#888;">Loading...</p>';

    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.onload = function(){
        if(xhr.status === 200){
            var data = JSON.parse(xhr.responseText);
            if(data.status === 'success'){
                renderTable(data.products);
                document.getElementById('result-info').textContent =
                    'Showing ' + data.count + ' product(s)';
            } else {
                document.getElementById('product-wrap').innerHTML =
                    '<div class="no-products">' + data.message + '</div>';
            }
        }
    };
    xhr.send();
}

function renderTable(products){
    if(products.length === 0){
        document.getElementById('product-wrap').innerHTML =
            '<div class="no-products">No products found.</div>';
        return;
    }

    var html = '<table>';
    html += '<tr>';
    html += '<th>Product Name &amp; Manufacturer Review</th>';
    html += '<th>Category</th>';
    html += '<th>Brand</th>';
    html += '<th>Price</th>';
    html += '<th>Stock</th>';
    html += '<th>Action</th>';
    html += '</tr>';

    for(var i = 0; i < products.length; i++){
        var p       = products[i];
        var inStock = parseInt(p.stock) > 0;
        var catLabel = p.parent_name
            ? p.parent_name + ' &rsaquo; ' + p.category_name
            : p.category_name;

        html += '<tr>';
        html += '<td><strong>' + escHtml(p.name) + '</strong>';
        if(p.manufacturer_review){
            html += '<div class="mfr-review">' + escHtml(p.manufacturer_review) + '</div>';
        }
        html += '</td>';
        html += '<td>' + catLabel + '</td>';
        html += '<td>' + (p.brand_name ? escHtml(p.brand_name) : '-') + '</td>';
        html += '<td class="price-col">Tk ' + parseFloat(p.price).toLocaleString() + '</td>';

        if(inStock){
            html += '<td><span class="stock-ok">' + p.stock + ' left</span></td>';
            html += '<td>'
                  + '<a class="btn-view" href="product_detail.php?id=' + p.id + '">View</a>'
                  + ' <button class="btn-add" onclick="addToCart(' + p.id + ')">+ Cart</button>'
                  + '</td>';
        } else {
            html += '<td><span class="stock-out">Out of stock</span></td>';
            html += '<td>'
                  + '<a class="btn-view" href="product_detail.php?id=' + p.id + '">View</a>'
                  + ' <button class="btn-add" disabled>+ Cart</button>'
                  + '</td>';
        }
        html += '</tr>';
    }
    html += '</table>';
    document.getElementById('product-wrap').innerHTML = html;
}

function addToCart(product_id){
    <?php if(!isset($_SESSION['user_id'])): ?>
        showMsg('Please login to add items to cart', 'error');
        return;
    <?php endif; ?>

    var fd = new FormData();
    fd.append('product_id', product_id);
    fd.append('quantity', 1);

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

function escHtml(str){
    var d = document.createElement('div');
    d.textContent = str || '';
    return d.innerHTML;
}

function showMsg(msg, type){
    var old = document.getElementById('ajax-msg');
    if(old) old.innerHTML = '';
    var el = document.getElementById('ajax-msg');
    el.textContent = msg;
    el.className   = 'alert-' + type;
    setTimeout(function(){ el.textContent = ''; el.className = ''; }, 3000);
}
</script>

</body>
</html>
