<?php
// public/api/admin/delete_customer.php  → POST /api/admin/delete_customer.php
session_start();
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../controllers/AdminController.php';

$controller = new AdminController();
$controller->deleteCustomer();
