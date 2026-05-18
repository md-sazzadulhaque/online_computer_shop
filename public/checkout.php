<?php
// public/checkout.php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/OrderController.php';

$controller = new OrderController();
$controller->checkout();
