<?php
require_once(__DIR__ . '/../config/db.php');

function addToCart($user_id, $product_id, $quantity){
    $pdo  = getDB();

    $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $row  = $stmt->fetch();

    if($row){
        $new_qty = $row['quantity'] + (int)$quantity;
        $stmt    = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        return $stmt->execute([$new_qty, $row['id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO cart(user_id, product_id, quantity) VALUES(?,?,?)");
        return $stmt->execute([$user_id, $product_id, (int)$quantity]);
    }
}

// Get all cart items for a user 
function getCartItems($user_id){
    $pdo  = getDB();
    $stmt = $pdo->prepare(
        "SELECT c.id AS cart_id, c.quantity,
                p.id AS product_id, p.name, p.price, p.stock, p.image_path
         FROM   cart c
         JOIN   products p ON c.product_id = p.id
         WHERE  c.user_id = ?"
    );
    $stmt->execute([$user_id]);
    $items = $stmt->fetchAll();

    foreach($items as &$item){
        $item['subtotal'] = $item['price'] * $item['quantity'];
    }

    return $items;
}

// Update quantity of one cart row
function updateCartItem($cart_id, $user_id, $quantity){
    $pdo  = getDB();
    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
    return $stmt->execute([(int)$quantity, $cart_id, $user_id]);
}

// Remove one cart row
function removeCartItem($cart_id, $user_id){
    $pdo  = getDB();
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    return $stmt->execute([$cart_id, $user_id]);
}

// Get cart grand total
function getCartTotal($user_id){
    $pdo  = getDB();
    $stmt = $pdo->prepare(
        "SELECT SUM(p.price * c.quantity) AS total
         FROM   cart c
         JOIN   products p ON c.product_id = p.id
         WHERE  c.user_id = ?"
    );
    $stmt->execute([$user_id]);
    $row  = $stmt->fetch();
    return $row['total'] ? number_format((float)$row['total'], 2) : '0.00';
}

// Get total item count in cart 
function getCartCount($user_id){
    $pdo  = getDB();
    $stmt = $pdo->prepare("SELECT SUM(quantity) AS total FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $row  = $stmt->fetch();
    return $row['total'] ? (int)$row['total'] : 0;
}
