<?php
// public/api/orders/place.php  → POST /api/orders/place.php
session_start();
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../controllers/OrderController.php';

$controller = new OrderController();
$controller->placeOrder();
