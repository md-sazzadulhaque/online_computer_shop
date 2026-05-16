<?php

session_start();

require_once(__DIR__ . '/../model/cartModel.php');
require_once(__DIR__ . '/../model/productModel.php');

header('Content-Type: application/json');

if(!isset($_SESSION['customer_id'])){
    echo json_encode(['status' => 'error', 'message' => 'Please login first']);
    exit;
}

$customer_id = $_SESSION['customer_id'];
$action      = isset($_GET['action']) ? $_GET['action'] : '';

if($action == 'add'){
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity   = isset($_POST['quantity'])   ? (int)$_POST['quantity']   : 1;

    if($product_id <= 0){
        echo json_encode(['status' => 'error', 'message' => 'Invalid product']);
        exit;
    }
    if($quantity <= 0){
        echo json_encode(['status' => 'error', 'message' => 'Quantity must be a positive number']);
        exit;
    }

    $product = getProductById($product_id);
    if(!$product){
        echo json_encode(['status' => 'error', 'message' => 'Product not found']);
        exit;
    }
    if($product['stock'] < $quantity){
        echo json_encode(['status' => 'error', 'message' => 'Not enough stock. Only ' . $product['stock'] . ' left']);
        exit;
    }

    $result = addToCart($customer_id, $product_id, $quantity);

    if($result){
        $count = getCartCount($customer_id);
        echo json_encode(['status' => 'success', 'message' => 'Item added to cart', 'cart_count' => $count]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Could not add to cart']);
    }
    exit;
}

if($action == 'update'){
    $cart_id  = isset($_POST['cart_id'])  ? (int)$_POST['cart_id']  : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

    if($quantity <= 0){
        echo json_encode(['status' => 'error', 'message' => 'Quantity must be a positive number']);
        exit;
    }

    $result = updateCartItem($cart_id, $customer_id, $quantity);

    if($result){
        $items = getCartItemsArray($customer_id);
        $total = getCartTotal($customer_id);
        $count = getCartCount($customer_id);
        echo json_encode(['status' => 'success', 'items' => $items, 'total' => $total, 'cart_count' => $count]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Could not update cart']);
    }
    exit;
}

//  REMOVE FROM CART 
if($action == 'remove'){
    $cart_id = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;

    $result = removeCartItem($cart_id, $customer_id);

    if($result){
        $items = getCartItemsArray($customer_id);
        $total = getCartTotal($customer_id);
        $count = getCartCount($customer_id);
        echo json_encode(['status' => 'success', 'items' => $items, 'total' => $total, 'cart_count' => $count]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Could not remove item']);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Unknown action']);
