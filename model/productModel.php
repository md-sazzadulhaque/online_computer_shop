<?php
    require_once('../config/db.php');

    function getAllProducts(){
        $con = getConnection();
        $sql = "SELECT p.*, c.name AS category_name, b.name AS brand_name
                FROM products p
                JOIN categories c ON p.category_id = c.id
                JOIN brands b ON p.brand_id = b.id
                ORDER BY p.created_at DESC";
        $result = mysqli_query($con, $sql);
        $list = array();
        while($row = mysqli_fetch_assoc($result)){
            array_push($list, $row);
        }
        return $list;
    }

    function getProductById($id){
        $con = getConnection();
        $id = mysqli_real_escape_string($con, $id);
        $sql = "SELECT * FROM products WHERE id = '$id'";
        $result = mysqli_query($con, $sql);
        return mysqli_fetch_assoc($result);
    }

    function addProduct($p){
        $con = getConnection();
        $name        = mysqli_real_escape_string($con, $p['name']);
        $description = mysqli_real_escape_string($con, $p['description']);
        $manuf       = mysqli_real_escape_string($con, $p['manufacturer_review']);
        $price       = mysqli_real_escape_string($con, $p['price']);
        $category_id = mysqli_real_escape_string($con, $p['category_id']);
        $brand_id    = mysqli_real_escape_string($con, $p['brand_id']);
        $image_path  = mysqli_real_escape_string($con, $p['image_path']);
        $stock       = mysqli_real_escape_string($con, $p['stock']);

        $sql = "INSERT INTO products
                (name, description, manufacturer_review, price, category_id, brand_id, image_path, stock)
                VALUES
                ('$name', '$description', '$manuf', '$price', '$category_id', '$brand_id', '$image_path', '$stock')";
        return mysqli_query($con, $sql);
    }

    function updateProduct($p){
        $con = getConnection();
        $id          = mysqli_real_escape_string($con, $p['id']);
        $name        = mysqli_real_escape_string($con, $p['name']);
        $description = mysqli_real_escape_string($con, $p['description']);
        $manuf       = mysqli_real_escape_string($con, $p['manufacturer_review']);
        $price       = mysqli_real_escape_string($con, $p['price']);
        $category_id = mysqli_real_escape_string($con, $p['category_id']);
        $brand_id    = mysqli_real_escape_string($con, $p['brand_id']);
        $image_path  = mysqli_real_escape_string($con, $p['image_path']);
        $stock       = mysqli_real_escape_string($con, $p['stock']);

        $sql = "UPDATE products SET
                    name = '$name',
                    description = '$description',
                    manufacturer_review = '$manuf',
                    price = '$price',
                    category_id = '$category_id',
                    brand_id = '$brand_id',
                    image_path = '$image_path',
                    stock = '$stock'
                WHERE id = '$id'";
        return mysqli_query($con, $sql);
    }

    function deleteProduct($id){
        $con = getConnection();
        // also delete the image file from disk
        $product = getProductById($id);
        if($product && $product['image_path'] != ""){
            $path = '../' . $product['image_path'];
            if(file_exists($path)){
                unlink($path);
            }
        }
        $id = mysqli_real_escape_string($con, $id);
        $sql = "DELETE FROM products WHERE id = '$id'";
        return mysqli_query($con, $sql);
    }

    function countProducts(){
        $con = getConnection();
        $result = mysqli_query($con, "SELECT COUNT(*) AS c FROM products");
        $row = mysqli_fetch_assoc($result);
        return $row['c'];
    }

    function getLowStockProducts($threshold){
        $con = getConnection();
        $threshold = mysqli_real_escape_string($con, $threshold);
        $sql = "SELECT * FROM products WHERE stock < '$threshold' ORDER BY stock ASC";
        $result = mysqli_query($con, $sql);
        $list = array();
        while($row = mysqli_fetch_assoc($result)){
            array_push($list, $row);
        }
        return $list;
    }
?>
