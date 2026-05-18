<?php
// public/orders/confirmation.php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../controllers/OrderController.php';

$controller = new OrderController();
$orderId    = (int) ($_GET['id'] ?? 0);

if ($orderId <= 0) {
    header('Location: /task4_23-51148-1/views/product_details.php?id=1');
    exit;
}

$controller->confirmation($orderId);
