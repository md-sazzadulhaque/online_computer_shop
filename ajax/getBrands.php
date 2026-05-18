<?php
    require_once('../config/auth.php');
    require_once('../model/brandModel.php');

    header('Content-Type: application/json');

    $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';
    if(!is_numeric($category_id)){
        echo json_encode(array('status' => 'error', 'message' => 'Invalid category id'));
        exit;
    }

    $brands = getBrandsByCategory($category_id);
    echo json_encode(array('status' => 'ok', 'brands' => $brands));
?>
