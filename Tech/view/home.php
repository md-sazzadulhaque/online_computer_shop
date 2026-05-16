<?php
session_start();
require_once('../model/productModel.php');
require_once('../model/cartModel.php');

// Load sidebar data into arrays
$catResult = getAllCategories();
$allCategories = [];
while($c = mysqli_fetch_assoc($catResult)) $allCategories[] = $c;

$subResult = getAllSubcategories();
$allSubcategories = [];
while($s = mysqli_fetch_assoc($subResult)) $allSubcategories[] = $s;

$brandResult = getAllBrands();
$allBrands = [];
while($b = mysqli_fetch_assoc($brandResult)) $allBrands[] = $b;

// Cart count for navbar badge
$cartCount = 0;
if(isset($_SESSION['customer_id'])){
    $cartCount = getCartCount($_SESSION['customer_id']);
}

$initResult   = getAllProducts();
$initProducts = [];
while($row = mysqli_fetch_assoc($initResult)) $initProducts[] = $row;
?>
<!DOCTYPE html>
<html>
<head>
    <title>TechShop - Home</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background-color: #f0f0f0; color: #222; }
        a { text-decoration: none; color: #222; }

        /* NAVBAR */
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

        /* LAYOUT */
        .wrapper {
            display: flex;
            max-width: 1200px;
            margin: 20px auto;
            gap: 16px;
            align-items: flex-start;
        }

        /* SIDEBAR */
        .sidebar {
            width: 200px;
            flex-shrink: 0;
            background: white;
            border: 1px solid #ddd;
            padding: 14px;
        }
        .sidebar h4 {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #999;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 8px;
            margin-top: 14px;
        }
        .sidebar h4:first-child { margin-top: 0; }
        .sidebar a {
            display: block;
            padding: 5px 6px;
            font-size: 13px;
            color: #444;
            border-radius: 3px;
        }
        .sidebar a:hover { background-color: #f0f0f0; }
        .sidebar a.active { background-color: #222; color: white; }
        .sidebar .sub-label {
            font-size: 11px;
            color: #aaa;
            margin-left: 4px;
        }

        /* MAIN */
        .main { flex: 1; }

        /* SEARCH BAR */
        .search-box {
            background: white;
            border: 1px solid #ddd;
            padding: 12px 14px;
            margin-bottom: 10px;
            display: flex;
            gap: 8px;
        }
        .search-box input[type="text"] {
            flex: 1;
            padding: 8px 10px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        .search-box button {
            padding: 8px 18px;
            background-color: #222;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .search-box button:hover { background-color: #444; }

        /* FILTER BAR */
        .filter-box {
            background: white;
            border: 1px solid #ddd;
            padding: 12px 14px;
            margin-bottom: 10px;
        }
        .filter-box .filter-title {
            font-size: 13px;
            font-weight: bold;
            color: #555;
            margin-bottom: 10px;
        }
        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: flex-end;
        }
        .filter-group { display: flex; flex-direction: column; gap: 4px; }
        .filter-group label { font-size: 12px; color: #777; font-weight: bold; }
        .filter-group input[type="number"],
        .filter-group select {
            padding: 7px 8px;
            border: 1px solid #ccc;
            font-size: 13px;
            width: 130px;
        }
        .filter-group select { width: 145px; }
        .btn-filter {
            padding: 7px 18px;
            background-color: #222;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 13px;
        }
        .btn-filter:hover { background-color: #444; }
        .btn-clear {
            padding: 7px 14px;
            background-color: #bbb;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 13px;
        }
        .btn-clear:hover { background-color: #999; }

        /* ALERTS */
        .success {
            padding: 8px 12px;
            background-color: #e6ffe6;
            border: 1px solid green;
            color: green;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .error {
            padding: 8px 12px;
            background-color: #ffe6e6;
            border: 1px solid red;
            color: red;
            margin-bottom: 10px;
            font-size: 14px;
        }

        /* RESULT INFO */
        .result-info {
            font-size: 13px;
            color: #666;
            margin-bottom: 8px;
        }

        /* PRODUCT TABLE */
        #product-table-wrap table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border: 1px solid #ddd;
        }
        #product-table-wrap table th {
            background-color: #222;
            color: white;
            padding: 10px 12px;
            text-align: left;
            font-size: 13px;
        }
        #product-table-wrap table td {
            padding: 10px 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            vertical-align: top;
        }
        #product-table-wrap table tr:hover td { background-color: #fafafa; }

        .mfr-review {
            font-size: 12px;
            color: #888;
            margin-top: 3px;
            font-style: italic;
        }
        .price-col { font-weight: bold; }
        .stock-ok  { color: green; font-size: 13px; }
        .stock-out { color: red;   font-size: 13px; }
        .btn-view {
            display: inline-block;
            padding: 5px 12px;
            background-color: #444;
            color: white;
            font-size: 12px;
        }
        .btn-view:hover { background-color: #222; }
        .btn-add {
            display: inline-block;
            padding: 5px 12px;
            background-color: #e67e00;
            color: white;
            font-size: 12px;
            border: none;
            cursor: pointer;
            margin-left: 4px;
        }
        .btn-add:hover { background-color: #c56a00; }
        .btn-add:disabled { background-color: #ccc; cursor: not-allowed; }

        .no-products {
            background: white;
            border: 1px solid #ddd;
            padding: 30px;
            text-align: center;
            color: #888;
            font-size: 15px;
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

<div class="wrapper">

    <div class="sidebar">
        <h4>Categories</h4>
        <a href="#" onclick="sidebarFilter('','','','')" class="active" id="link-all">All Products</a>
        <?php foreach($allCategories as $cat): ?>
            <a href="#" onclick="sidebarFilter('<?php echo $cat['id']; ?>','','','')"
               id="link-cat-<?php echo $cat['id']; ?>">
                <?php echo $cat['name']; ?>
            </a>
        <?php endforeach; ?>

        <h4>Subcategories</h4>
        <?php foreach($allSubcategories as $sub): ?>
            <a href="#" onclick="sidebarFilter('','<?php echo $sub['id']; ?>','','')"
               id="link-sub-<?php echo $sub['id']; ?>">
                <?php echo $sub['name']; ?>
                <span class="sub-label">(<?php echo $sub['category_name']; ?>)</span>
            </a>
        <?php endforeach; ?>

        <h4>Brands</h4>
        <?php foreach($allBrands as $brand): ?>
            <a href="#" onclick="sidebarFilter('','','<?php echo $brand['id']; ?>','')"
               id="link-brand-<?php echo $brand['id']; ?>">
                <?php echo $brand['name']; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- MAIN -->
    <div class="main">

        <?php if(isset($_GET['success'])): ?>
            <div class="success"><?php echo $_GET['success']; ?></div>
        <?php endif; ?>
        <?php if(isset($_GET['error'])): ?>
            <div class="error"><?php echo $_GET['error']; ?></div>
        <?php endif; ?>

        <!-- SEARCH BOX -->
        <div class="search-box">
            <input type="text" id="search-input" placeholder="Search products, brands, categories...">
            <button onclick="doSearch()">Search</button>
        </div>

        <!-- FILTER BOX -->
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
                        <?php foreach($allCategories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Subcategory</label>
                    <select id="filter-sub">
                        <option value="">All Subcategories</option>
                        <?php foreach($allSubcategories as $sub): ?>
                            <option value="<?php echo $sub['id']; ?>">
                                <?php echo $sub['name']; ?> (<?php echo $sub['category_name']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Brand</label>
                    <select id="filter-brand">
                        <option value="">All Brands</option>
                        <?php foreach($allBrands as $brand): ?>
                            <option value="<?php echo $brand['id']; ?>"><?php echo $brand['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="btn-filter" onclick="doFilter()">Apply Filter</button>
                <button class="btn-clear"  onclick="clearAll()">Clear</button>
            </div>
            <p id="filter-error" style="color:red; font-size:13px; margin-top:8px; display:none;"></p>
        </div>

        <!-- RESULT COUNT -->
        <p class="result-info" id="result-info">
            Showing <?php echo count($initProducts); ?> product(s)
        </p>

        <!-- PRODUCT TABLE (updated by JS) -->
        <div id="product-table-wrap">
            <?php if(empty($initProducts)): ?>
                <div class="no-products">No products found.</div>
            <?php else: ?>
            <table>
                <tr>
                    <th>Product Name &amp; Review</th>
                    <th>Category</th>
                    <th>Subcategory</th>
                    <th>Brand</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Action</th>
                </tr>
                <?php foreach($initProducts as $p): ?>
                <tr>
                    <td>
                        <strong><?php echo $p['name']; ?></strong>
                        <div class="mfr-review"><?php echo $p['manufacturer_review']; ?></div>
                    </td>
                    <td><?php echo $p['category_name']; ?></td>
                    <td><?php echo $p['subcategory_name'] ? $p['subcategory_name'] : '-'; ?></td>
                    <td><?php echo $p['brand_name'] ? $p['brand_name'] : '-'; ?></td>
                    <td class="price-col">Tk <?php echo number_format($p['price'],2); ?></td>
                    <td>
                        <?php if($p['stock'] > 0): ?>
                            <span class="stock-ok"><?php echo $p['stock']; ?> left</span>
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
    var sub = document.getElementById('filter-sub').value;
    var brd = document.getElementById('filter-brand').value;
    callSearchAPI(q, min, max, cat, sub, brd);
}

function doFilter(){
    var errEl = document.getElementById('filter-error');
    errEl.style.display = 'none';

    var min = document.getElementById('filter-min').value;
    var max = document.getElementById('filter-max').value;

    // JS validation: price must be numbers
    if(min !== '' && isNaN(parseFloat(min))){
        errEl.textContent = 'Min price must be a number';
        errEl.style.display = 'block';
        return;
    }
    if(max !== '' && isNaN(parseFloat(max))){
        errEl.textContent = 'Max price must be a number';
        errEl.style.display = 'block';
        return;
    }
    if(min !== '' && max !== '' && parseFloat(min) > parseFloat(max)){
        errEl.textContent = 'Min price cannot be greater than max price';
        errEl.style.display = 'block';
        return;
    }

    doSearch();
}

// Called when sidebar category/subcategory/brand link clicked
function sidebarFilter(cat_id, sub_id, brand_id, keyword){
    // Reset filter inputs
    document.getElementById('search-input').value = keyword;
    document.getElementById('filter-cat').value   = cat_id;
    document.getElementById('filter-sub').value   = sub_id;
    document.getElementById('filter-brand').value = brand_id;
    document.getElementById('filter-min').value   = '';
    document.getElementById('filter-max').value   = '';
    markActive(cat_id, sub_id, brand_id);
    callSearchAPI(keyword, '', '', cat_id, sub_id, brand_id);
}

function markActive(cat_id, sub_id, brand_id){
    // Remove all active classes first
    document.querySelectorAll('.sidebar a').forEach(function(a){ a.classList.remove('active'); });
    if(cat_id   === '' && sub_id === '' && brand_id === '') document.getElementById('link-all').classList.add('active');
    if(cat_id   !== '') { var el = document.getElementById('link-cat-'   + cat_id);   if(el) el.classList.add('active'); }
    if(sub_id   !== '') { var el = document.getElementById('link-sub-'   + sub_id);   if(el) el.classList.add('active'); }
    if(brand_id !== '') { var el = document.getElementById('link-brand-' + brand_id); if(el) el.classList.add('active'); }
}

// Clear all filters and reload all products
function clearAll(){
    document.getElementById('search-input').value = '';
    document.getElementById('filter-min').value   = '';
    document.getElementById('filter-max').value   = '';
    document.getElementById('filter-cat').value   = '';
    document.getElementById('filter-sub').value   = '';
    document.getElementById('filter-brand').value = '';
    document.getElementById('filter-error').style.display = 'none';
    markActive('','','');
    callSearchAPI('','','','','','');
}

function callSearchAPI(q, min, max, cat, sub, brd){
    var url = '../api/search.php?q=' + encodeURIComponent(q)
            + '&min_price='      + encodeURIComponent(min)
            + '&max_price='      + encodeURIComponent(max)
            + '&category_id='    + encodeURIComponent(cat)
            + '&subcategory_id=' + encodeURIComponent(sub)
            + '&brand_id='       + encodeURIComponent(brd);

    document.getElementById('product-table-wrap').innerHTML =
        '<p style="padding:20px; color:#888;">Loading...</p>';

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
                document.getElementById('product-table-wrap').innerHTML =
                    '<div class="no-products">' + data.message + '</div>';
            }
        }
    };
    xhr.send();
}

function renderTable(products){
    var wrap = document.getElementById('product-table-wrap');

    if(products.length === 0){
        wrap.innerHTML = '<div class="no-products">No products found.</div>';
        return;
    }

    var html = '<table>';
    html += '<tr>';
    html += '<th>Product Name &amp; Review</th>';
    html += '<th>Category</th>';
    html += '<th>Subcategory</th>';
    html += '<th>Brand</th>';
    html += '<th>Price</th>';
    html += '<th>Stock</th>';
    html += '<th>Action</th>';
    html += '</tr>';

    for(var i = 0; i < products.length; i++){
        var p = products[i];
        var inStock = parseInt(p.stock) > 0;

        html += '<tr>';
        html += '<td><strong>' + p.name + '</strong>';
        if(p.manufacturer_review){
            html += '<div class="mfr-review">' + p.manufacturer_review + '</div>';
        }
        html += '</td>';
        html += '<td>' + (p.category_name    || '-') + '</td>';
        html += '<td>' + (p.subcategory_name || '-') + '</td>';
        html += '<td>' + (p.brand_name       || '-') + '</td>';
        html += '<td class="price-col">Tk ' + parseFloat(p.price).toLocaleString() + '</td>';

        if(inStock){
            html += '<td><span class="stock-ok">' + p.stock + ' left</span></td>';
            html += '<td>';
            html += '<a class="btn-view" href="product_detail.php?id=' + p.id + '">View</a>';
            html += ' <button class="btn-add" onclick="addToCart(' + p.id + ')">+ Cart</button>';
            html += '</td>';
        } else {
            html += '<td><span class="stock-out">Out of stock</span></td>';
            html += '<td>';
            html += '<a class="btn-view" href="product_detail.php?id=' + p.id + '">View</a>';
            html += ' <button class="btn-add" disabled>+ Cart</button>';
            html += '</td>';
        }

        html += '</tr>';
    }

    html += '</table>';
    wrap.innerHTML = html;
}


function addToCart(product_id){
    <?php if(!isset($_SESSION['customer_id'])): ?>
        showMsg('Please login to add items to cart', 'error');
        return;
    <?php endif; ?>

    var formData = new FormData();
    formData.append('product_id', product_id);
    formData.append('quantity',   1);

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
    var old = document.getElementById('ajax-msg');
    if(old) old.remove();

    var div = document.createElement('div');
    div.id = 'ajax-msg';
    div.textContent = msg;
    div.className = type;
    div.style.marginBottom = '10px';

    var main = document.querySelector('.main');
    main.insertBefore(div, main.firstChild);

    setTimeout(function(){ div.remove(); }, 3000);
}
</script>

</body>
</html>
