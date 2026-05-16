<?php

require_once(__DIR__ . '/../db.php');

function addToCart($customer_id, $product_id, $quantity){
    $con         = getConnection();
    $customer_id = mysqli_real_escape_string($con, $customer_id);
    $product_id  = mysqli_real_escape_string($con, $product_id);
    $quantity    = (int)$quantity;

    $check  = "SELECT * FROM cart WHERE customer_id='$customer_id' AND product_id='$product_id'";
    $result = mysqli_query($con, $check);

    if($result && mysqli_num_rows($result) > 0){
        $row     = mysqli_fetch_assoc($result);
        $new_qty = $row['quantity'] + $quantity;
        $sql = "UPDATE cart SET quantity='$new_qty'
                WHERE customer_id='$customer_id' AND product_id='$product_id'";
    } else {
        $sql = "INSERT INTO cart(customer_id, product_id, quantity)
                VALUES('$customer_id', '$product_id', '$quantity')";
    }

    return mysqli_query($con, $sql);
}


function getCartItems($customer_id){
    $con         = getConnection();
    $customer_id = mysqli_real_escape_string($con, $customer_id);
    $sql = "SELECT c.id as cart_id, c.quantity, p.id as product_id,
                   p.name, p.price, p.stock
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.customer_id='$customer_id'";
    return mysqli_query($con, $sql);
}

function getCartItemsArray($customer_id){
    $con         = getConnection();
    $customer_id = mysqli_real_escape_string($con, $customer_id);
    $sql = "SELECT c.id as cart_id, c.quantity, p.id as product_id,
                   p.name, p.price, p.stock
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.customer_id='$customer_id'";
    $result = mysqli_query($con, $sql);
    $items  = [];
    while($row = mysqli_fetch_assoc($result)){
        $row['subtotal'] = $row['price'] * $row['quantity'];
        $items[] = $row;
    }
    return $items;
}

function updateCartItem($cart_id, $customer_id, $quantity){
    $con         = getConnection();
    $cart_id     = mysqli_real_escape_string($con, $cart_id);
    $customer_id = mysqli_real_escape_string($con, $customer_id);
    $quantity    = (int)$quantity;
    $sql = "UPDATE cart SET quantity='$quantity'
            WHERE id='$cart_id' AND customer_id='$customer_id'";
    return mysqli_query($con, $sql);
}

function removeCartItem($cart_id, $customer_id){
    $con         = getConnection();
    $cart_id     = mysqli_real_escape_string($con, $cart_id);
    $customer_id = mysqli_real_escape_string($con, $customer_id);
    $sql = "DELETE FROM cart WHERE id='$cart_id' AND customer_id='$customer_id'";
    return mysqli_query($con, $sql);
}

function getCartTotal($customer_id){
    $con         = getConnection();
    $customer_id = mysqli_real_escape_string($con, $customer_id);
    $sql    = "SELECT SUM(p.price * c.quantity) as total
               FROM cart c
               JOIN products p ON c.product_id = p.id
               WHERE c.customer_id='$customer_id'";
    $result = mysqli_query($con, $sql);
    $row    = mysqli_fetch_assoc($result);
    return $row['total'] ? number_format($row['total'], 2) : '0.00';
}

function getCartCount($customer_id){
    $con         = getConnection();
    $customer_id = mysqli_real_escape_string($con, $customer_id);
    $sql    = "SELECT SUM(quantity) as total FROM cart WHERE customer_id='$customer_id'";
    $result = mysqli_query($con, $sql);
    $row    = mysqli_fetch_assoc($result);
    return $row['total'] ? (int)$row['total'] : 0;
}
