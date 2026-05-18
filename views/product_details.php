<?php
// view/product_details.php

session_start();

require_once(__DIR__ . '/../model/productModel.php');
require_once(__DIR__ . '/../model/CartModel.php');
require_once(__DIR__ . '/../model/ReviewModel.php'); // Task 4

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

// Task 4 — load reviews
$reviewModel = new ReviewModel();
$reviews = $reviewModel->getByProduct($product['id']);
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

        /* ── Task 4: Reviews Section ─────────────────────────── */
        .reviews-section {
            margin-top: 36px;
            border-top: 2px solid #eee;
            padding-top: 24px;
        }
        .reviews-section h3 {
            font-size: 18px;
            margin-bottom: 16px;
            color: #222;
        }
        .review-count {
            display: inline-block;
            background: #e67e00;
            color: white;
            border-radius: 12px;
            padding: 1px 9px;
            font-size: 13px;
            font-weight: bold;
            margin-left: 6px;
        }
        .review-card {
            border: 1px solid #eee;
            border-radius: 4px;
            padding: 12px 16px;
            margin-bottom: 10px;
            background: #fafafa;
            position: relative;
        }
        .review-card .reviewer-name {
            font-weight: bold;
            font-size: 14px;
            color: #333;
        }
        .review-card .review-date {
            font-size: 12px;
            color: #999;
            margin-left: 8px;
        }
        .review-card .review-text {
            margin-top: 6px;
            font-size: 14px;
            color: #444;
            line-height: 1.6;
        }
        .btn-del-review {
            position: absolute;
            top: 10px; right: 12px;
            background: #cc0000;
            color: white;
            border: none;
            padding: 3px 10px;
            font-size: 12px;
            cursor: pointer;
            border-radius: 3px;
        }
        .btn-del-review:hover { background: #990000; }
        #review-flash { margin-bottom: 10px; font-size: 13px; }
        .no-reviews { color: #999; font-size: 14px; margin-bottom: 16px; }

        .review-form-box {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 18px;
            background: #fff;
            margin-top: 18px;
        }
        .review-form-box h4 { font-size: 15px; margin-bottom: 14px; color: #333; }
        .review-form-box label { font-size: 13px; font-weight: bold; display: block; margin-bottom: 4px; }
        .review-form-box input[type="text"] {
            width: 100%; padding: 7px 10px;
            border: 1px solid #ccc; font-size: 14px;
            margin-bottom: 12px; background: #f5f5f5;
        }
        .review-form-box textarea {
            width: 100%; padding: 8px 10px;
            border: 1px solid #ccc; font-size: 14px;
            resize: vertical; min-height: 90px;
            margin-bottom: 4px;
        }
        .review-form-box textarea:focus { outline: none; border-color: #e67e00; }
        .char-count { font-size: 12px; color: #999; text-align: right; margin-bottom: 10px; }
        #review-comment-error { color: red; font-size: 12px; margin-bottom: 8px; display: none; }
        .btn-post-review {
            padding: 8px 22px;
            background: #222; color: white;
            border: none; cursor: pointer;
            font-size: 14px; font-weight: bold;
        }
        .btn-post-review:hover { background: #444; }
        .login-to-review { font-size: 13px; color: #888; margin-top: 14px; }
        .login-to-review a { color: #e67e00; }
    </style>
</head>
<body>

<div class="navbar">
    <span class="logo">TechShop</span>
    <a href="/task4_23-51148-1/public/test_login.php">Home</a>
    <a href="/task4_23-51148-1/views/cart.php" class="cart-link">
        Cart (<span id="nav-cart-count"><?php echo $cartCount; ?></span>)
    </a>
    <div class="nav-right">
        <?php if(isset($_SESSION['user_id'])): ?>
            Hi, <b style="color:white"><?php echo htmlspecialchars($_SESSION['name']); ?></b>
            &nbsp;|&nbsp;
            <a href="/task4_23-51148-1/public/test_login.php?as=logout">Logout</a>
        <?php else: ?>
            <a href="/task4_23-51148-1/public/test_login.php">Login</a>
            <a href="/task4_23-51148-1/public/test_login.php">Register</a>
        <?php endif; ?>
    </div>
</div>

<div class="container">

    <div id="ajax-msg"></div>

    <h2><?php echo htmlspecialchars($product['name']); ?></h2>

    <!-- Product Image -->
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

    <a class="btn-back" href="/task4_23-51148-1/public/test_login.php">&larr; Back to Home</a>

    <!-- ═══════════════════════════════════════════════════════
         TASK 4 — Customer Reviews Section
    ════════════════════════════════════════════════════════ -->
    <div class="reviews-section" id="reviews-section">
        <h3>
            Customer Reviews
            <span class="review-count" id="review-count"><?php echo count($reviews); ?></span>
        </h3>

        <div id="review-flash"></div>

        <!-- Existing reviews list -->
        <div id="reviews-list">
            <?php if(empty($reviews)): ?>
                <p class="no-reviews" id="no-reviews-msg">No reviews yet. Be the first to review!</p>
            <?php else: ?>
                <?php foreach($reviews as $rv): ?>
                <div class="review-card" id="review-<?php echo (int)$rv['id']; ?>">
                    <span class="reviewer-name"><?php echo htmlspecialchars($rv['reviewer_name']); ?></span>
                    <span class="review-date"><?php echo date('M d, Y', strtotime($rv['created_at'])); ?></span>
                    <?php
                    $canDelete = isset($_SESSION['user_id']) && (
                        ($_SESSION['role'] ?? '') === 'admin' ||
                        (int)$_SESSION['user_id'] === (int)$rv['user_id']
                    );
                    ?>
                    <?php if($canDelete): ?>
                        <button class="btn-del-review" onclick="deleteReview(<?php echo (int)$rv['id']; ?>)">Delete</button>
                    <?php endif; ?>
                    <p class="review-text"><?php echo nl2br(htmlspecialchars($rv['comment'])); ?></p>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Post review form — customers only -->
        <?php if(isset($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'customer'): ?>
        <div class="review-form-box">
            <h4>Write a Review</h4>
            <label>Your Name</label>
            <input type="text" value="<?php echo htmlspecialchars($_SESSION['name'] ?? ''); ?>" readonly>

            <label>Comment <span style="color:red">*</span></label>
            <textarea id="review-comment" placeholder="Share your experience with this product..." maxlength="1000"></textarea>
            <div class="char-count"><span id="char-count">0</span>/1000</div>
            <div id="review-comment-error">Comment must be at least 3 characters.</div>

            <button class="btn-post-review" onclick="submitReview(<?php echo (int)$product['id']; ?>)">Post Review</button>
        </div>

        <?php elseif(!isset($_SESSION['user_id'])): ?>
            <p class="login-to-review"><a href="/task4_23-51148-1/public/test_login.php">Login</a> to post a review.</p>
        <?php endif; ?>
    </div>
    <!-- END Task 4 Reviews Section -->

</div>

<script>
// ── Existing cart logic (unchanged) ──────────────────────────
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
    xhr.open('POST', '/task4_23-51148-1/public/api/cart.php?action=add', true);
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

// ── Task 4: Review JS ─────────────────────────────────────────
function escHtml(str){
    return String(str)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function showReviewMsg(msg, type){
    var el = document.getElementById('review-flash');
    el.textContent = msg;
    el.className   = 'alert-' + type;
    setTimeout(function(){ el.textContent = ''; el.className = ''; }, 4000);
}

// Character counter
var commentBox = document.getElementById('review-comment');
if(commentBox){
    commentBox.addEventListener('input', function(){
        document.getElementById('char-count').textContent = this.value.length;
    });
}

function submitReview(productId){
    var errEl   = document.getElementById('review-comment-error');
    var comment = commentBox ? commentBox.value.trim() : '';

    // JS validation
    errEl.style.display = 'none';
    if(comment.length < 3){
        errEl.style.display = 'block';
        commentBox.focus();
        return;
    }

    var fd = new FormData();
    fd.append('product_id', productId);
    fd.append('comment', comment);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/task4_23-51148-1/public/api/reviews/add.php', true);
    xhr.onload = function(){
        var data = JSON.parse(xhr.responseText);
        if(data.success === true){
            var noMsg = document.getElementById('no-reviews-msg');
            if(noMsg) noMsg.remove();

            // Prepend new review card
            var list = document.getElementById('reviews-list');
            var div  = document.createElement('div');
            div.className = 'review-card';
            div.id        = 'review-' + data.review_id;
            div.innerHTML =
                '<span class="reviewer-name">' + escHtml(data.reviewer_name) + '</span>' +
                '<span class="review-date"> just now</span>' +
                '<button class="btn-del-review" onclick="deleteReview(' + data.review_id + ')">Delete</button>' +
                '<p class="review-text">' + escHtml(data.comment).replace(/\n/g,'<br>') + '</p>';
            list.insertAdjacentElement('afterbegin', div);

            // Update count badge
            var badge = document.getElementById('review-count');
            badge.textContent = parseInt(badge.textContent) + 1;

            commentBox.value = '';
            document.getElementById('char-count').textContent = '0';
            showReviewMsg('Review posted successfully!', 'success');
        } else {
            showReviewMsg(data.error || 'Failed to post review.', 'error');
        }
    };
    xhr.onerror = function(){ showReviewMsg('Network error. Try again.', 'error'); };
    xhr.send(fd);
}

function deleteReview(reviewId){
    if(!confirm('Delete this review?')) return;

    var fd = new FormData();
    fd.append('review_id', reviewId);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/task4_23-51148-1/public/api/reviews/delete.php', true);
    xhr.onload = function(){
        var data = JSON.parse(xhr.responseText);
        if(data.success === true){
            var card = document.getElementById('review-' + reviewId);
            if(card) card.remove();
            var badge = document.getElementById('review-count');
            badge.textContent = Math.max(0, parseInt(badge.textContent) - 1);
            showReviewMsg('Review deleted.', 'success');
        } else {
            showReviewMsg(data.error || 'Failed to delete.', 'error');
        }
    };
    xhr.onerror = function(){ showReviewMsg('Network error. Try again.', 'error'); };
    xhr.send(fd);
}
</script>

</body>
</html>