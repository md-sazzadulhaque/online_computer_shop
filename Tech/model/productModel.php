<?php
require_once('../db.php');

function getAllProducts(){
    $con = getConnection();
    $sql = "SELECT p.*, c.name as category_name, s.name as subcategory_name, b.name as brand_name
            FROM products p
            LEFT JOIN categories c    ON p.category_id    = c.id
            LEFT JOIN subcategories s ON p.subcategory_id = s.id
            LEFT JOIN brands b        ON p.brand_id       = b.id
            ORDER BY p.id";
    return mysqli_query($con, $sql);
}

function getProductById($id){
    $con = getConnection();
    $id  = mysqli_real_escape_string($con, $id);
    $sql = "SELECT p.*, c.name as category_name, s.name as subcategory_name, b.name as brand_name
            FROM products p
            LEFT JOIN categories c    ON p.category_id    = c.id
            LEFT JOIN subcategories s ON p.subcategory_id = s.id
            LEFT JOIN brands b        ON p.brand_id       = b.id
            WHERE p.id='$id'";
    $result = mysqli_query($con, $sql);
    if($result && mysqli_num_rows($result) == 1){
        return mysqli_fetch_assoc($result);
    }
    return false;
}

function getAllCategories(){
    $con = getConnection();
    return mysqli_query($con, "SELECT * FROM categories ORDER BY name");
}

function getAllSubcategories(){
    $con = getConnection();
    $sql = "SELECT s.*, c.name as category_name
            FROM subcategories s
            JOIN categories c ON s.category_id = c.id
            ORDER BY c.name, s.name";
    return mysqli_query($con, $sql);
}

function getAllBrands(){
    $con = getConnection();
    return mysqli_query($con, "SELECT * FROM brands ORDER BY name");
}

function getProductsByCategory($category_id){
    $con         = getConnection();
    $category_id = mysqli_real_escape_string($con, $category_id);
    $sql = "SELECT p.*, c.name as category_name, s.name as subcategory_name, b.name as brand_name
            FROM products p
            LEFT JOIN categories c    ON p.category_id    = c.id
            LEFT JOIN subcategories s ON p.subcategory_id = s.id
            LEFT JOIN brands b        ON p.brand_id       = b.id
            WHERE p.category_id='$category_id'
            ORDER BY p.id";
    return mysqli_query($con, $sql);
}

function getProductsBySubcategory($subcategory_id){
    $con            = getConnection();
    $subcategory_id = mysqli_real_escape_string($con, $subcategory_id);
    $sql = "SELECT p.*, c.name as category_name, s.name as subcategory_name, b.name as brand_name
            FROM products p
            LEFT JOIN categories c    ON p.category_id    = c.id
            LEFT JOIN subcategories s ON p.subcategory_id = s.id
            LEFT JOIN brands b        ON p.brand_id       = b.id
            WHERE p.subcategory_id='$subcategory_id'
            ORDER BY p.id";
    return mysqli_query($con, $sql);
}

function getProductsByBrand($brand_id){
    $con      = getConnection();
    $brand_id = mysqli_real_escape_string($con, $brand_id);
    $sql = "SELECT p.*, c.name as category_name, s.name as subcategory_name, b.name as brand_name
            FROM products p
            LEFT JOIN categories c    ON p.category_id    = c.id
            LEFT JOIN subcategories s ON p.subcategory_id = s.id
            LEFT JOIN brands b        ON p.brand_id       = b.id
            WHERE p.brand_id='$brand_id'
            ORDER BY p.id";
    return mysqli_query($con, $sql);
}

function getSubcategoryById($id){
    $con = getConnection();
    $id  = mysqli_real_escape_string($con, $id);
    $sql = "SELECT s.*, c.name as category_name
            FROM subcategories s
            JOIN categories c ON s.category_id = c.id
            WHERE s.id='$id'";
    $result = mysqli_query($con, $sql);
    if($result && mysqli_num_rows($result) == 1) return mysqli_fetch_assoc($result);
    return false;
}

function getBrandById($id){
    $con = getConnection();
    $id  = mysqli_real_escape_string($con, $id);
    $result = mysqli_query($con, "SELECT * FROM brands WHERE id='$id'");
    if($result && mysqli_num_rows($result) == 1) return mysqli_fetch_assoc($result);
    return false;
}


function searchAndFilter($keyword, $min_price, $max_price, $category_id, $subcategory_id, $brand_id){
    $con = getConnection();

    $keyword        = mysqli_real_escape_string($con, $keyword);
    $min_price      = mysqli_real_escape_string($con, $min_price);
    $max_price      = mysqli_real_escape_string($con, $max_price);
    $category_id    = mysqli_real_escape_string($con, $category_id);
    $subcategory_id = mysqli_real_escape_string($con, $subcategory_id);
    $brand_id       = mysqli_real_escape_string($con, $brand_id);

    $where = "WHERE 1=1";

    if($keyword != ""){
        $where .= " AND (p.name LIKE '%$keyword%'
                    OR p.description LIKE '%$keyword%'
                    OR b.name LIKE '%$keyword%'
                    OR c.name LIKE '%$keyword%')";
    }
    if($min_price != "")      $where .= " AND p.price >= '$min_price'";
    if($max_price != "")      $where .= " AND p.price <= '$max_price'";
    if($category_id != "")    $where .= " AND p.category_id = '$category_id'";
    if($subcategory_id != "") $where .= " AND p.subcategory_id = '$subcategory_id'";
    if($brand_id != "")       $where .= " AND p.brand_id = '$brand_id'";

    $sql = "SELECT p.*, c.name as category_name, s.name as subcategory_name, b.name as brand_name
            FROM products p
            LEFT JOIN categories c    ON p.category_id    = c.id
            LEFT JOIN subcategories s ON p.subcategory_id = s.id
            LEFT JOIN brands b        ON p.brand_id       = b.id
            $where
            ORDER BY p.price ASC";

    $result = mysqli_query($con, $sql);
    $data   = [];
    while($row = mysqli_fetch_assoc($result)){
        $data[] = $row;
    }
    return $data;
}
