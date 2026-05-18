<?php
// public/api/cart.php — Simple cart API for task4 standalone testing
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../model/CartModel.php';

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Please login first.']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$userId = (int) $_SESSION['user_id'];

if ($action === 'add') {
    $productId = (int) ($_POST['product_id'] ?? 0);
    $quantity  = (int) ($_POST['quantity'] ?? 1);

    if ($productId <= 0 || $quantity <= 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid product or quantity.']);
        exit;
    }

    // Check product exists and has stock
    $pdo  = getDB();
    $stmt = $pdo->prepare("SELECT id, name, stock FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if (!$product) {
        echo json_encode(['status' => 'error', 'message' => 'Product not found.']);
        exit;
    }
    if ($product['stock'] < $quantity) {
        echo json_encode(['status' => 'error', 'message' => 'Not enough stock available.']);
        exit;
    }

    $cartModel = new CartModel();
    $cartModel->addToCart($userId, $productId, $quantity);
    $cartCount = $cartModel->getCartCount($userId);

    echo json_encode([
        'status'     => 'success',
        'message'    => htmlspecialchars($product['name']) . ' added to cart!',
        'cart_count' => $cartCount,
    ]);

} elseif ($action === 'update') {
    $cartId   = (int) ($_POST['cart_id'] ?? 0);
    $quantity = (int) ($_POST['quantity'] ?? 0);

    if ($cartId <= 0 || $quantity <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data.']);
        exit;
    }

    $cartModel = new CartModel();
    $cartModel->updateCartItem($cartId, $userId, $quantity);
    $total = $cartModel->getCartTotal($userId);
    $count = $cartModel->getCartCount($userId);

    echo json_encode(['status' => 'success', 'total' => $total, 'cart_count' => $count]);

} elseif ($action === 'remove') {
    $cartId = (int) ($_POST['cart_id'] ?? 0);

    if ($cartId <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid cart item.']);
        exit;
    }

    $cartModel = new CartModel();
    $cartModel->removeCartItem($cartId, $userId);
    $total = $cartModel->getCartTotal($userId);
    $count = $cartModel->getCartCount($userId);

    echo json_encode(['status' => 'success', 'total' => $total, 'cart_count' => $count]);

} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Unknown action.']);
}
