<?php


require_once('../model/productModel.php');

header('Content-Type: application/json');

$keyword        = isset($_GET['q'])              ? trim($_GET['q'])              : '';
$min_price      = isset($_GET['min_price'])      ? trim($_GET['min_price'])      : '';
$max_price      = isset($_GET['max_price'])      ? trim($_GET['max_price'])      : '';
$category_id    = isset($_GET['category_id'])    ? trim($_GET['category_id'])    : '';
$subcategory_id = isset($_GET['subcategory_id']) ? trim($_GET['subcategory_id']) : '';
$brand_id       = isset($_GET['brand_id'])       ? trim($_GET['brand_id'])       : '';

// PHP validation - price must be numbers
if($min_price != '' && !is_numeric($min_price)){
    echo json_encode(['status' => 'error', 'message' => 'Min price must be a number']);
    exit;
}
if($max_price != '' && !is_numeric($max_price)){
    echo json_encode(['status' => 'error', 'message' => 'Max price must be a number']);
    exit;
}

$products = searchAndFilter($keyword, $min_price, $max_price, $category_id, $subcategory_id, $brand_id);

echo json_encode([
    'status'   => 'success',
    'count'    => count($products),
    'products' => $products
]);
