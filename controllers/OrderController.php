<?php
// controllers/OrderController.php

require_once __DIR__ . '/../model/OrderModel.php';
require_once __DIR__ . '/../model/CartModel.php';

class OrderController {

    private OrderModel $orderModel;
    private CartModel  $cartModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
        $this->cartModel  = new CartModel();
    }

    /** Show checkout page (GET) */
    public function checkout(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (empty($_SESSION['user_id'])) {
            header('Location: /task4_23-51148-1/public/test_login.php');
            exit;
        }

        $userId    = (int) $_SESSION['user_id'];
        $cartItems = $this->cartModel->getCartItems($userId);

        if (empty($cartItems)) {
            $_SESSION['flash_error'] = 'Your cart is empty.';
            header('Location: /task4_23-51148-1/views/cart.php');
            exit;
        }

        $total = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cartItems));
        require __DIR__ . '/../views/orders/checkout.php';
    }

    /** Place order (POST) */
    public function placeOrder(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json');

        if (empty($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Not authenticated.']);
            return;
        }

        $userId        = (int) $_SESSION['user_id'];
        $paymentMethod = trim($_POST['payment_method'] ?? '');

        // Server-side validation
        $allowedMethods = ['cash_on_delivery', 'online_wallet'];
        if (!in_array($paymentMethod, $allowedMethods, true)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid payment method.']);
            return;
        }

        $cartItems = $this->cartModel->getCartItems($userId);
        if (empty($cartItems)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Cart is empty.']);
            return;
        }

        $total = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cartItems));

        // Prepare items for order model
        $items = array_map(fn($i) => [
            'product_id' => $i['product_id'],
            'quantity'   => $i['quantity'],
            'price'      => $i['price'],
        ], $cartItems);

        try {
            $orderId = $this->orderModel->placeOrder($userId, $items, $total, $paymentMethod);
            echo json_encode(['success' => true, 'order_id' => $orderId]);
        } catch (RuntimeException $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /** Order confirmation page */
    public function confirmation(int $orderId): void {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (empty($_SESSION['user_id'])) {
            header('Location: /task4_23-51148-1/public/test_login.php');
            exit;
        }

        $order = $this->orderModel->getOrderWithItems($orderId);

        if (!$order || (int) $order['user_id'] !== (int) $_SESSION['user_id']) {
            http_response_code(403);
            echo "<p>Order not found.</p>";
            return;
        }

        require __DIR__ . '/../views/orders/confirmation.php';
    }
}
