<?php
    require_once('../config/db.php');

    function getAllBrands(){
        $con = getConnection();
        $sql = "SELECT b.*, c.name AS category_name
                FROM brands b
                JOIN categories c ON b.category_id = c.id
                ORDER BY c.name, b.name";
        $result = mysqli_query($con, $sql);
        $list = array();
        while($row = mysqli_fetch_assoc($result)){
            array_push($list, $row);
        }
        return $list;
    }

    function getBrandsByCategory($category_id){
        $con = getConnection();
        $category_id = mysqli_real_escape_string($con, $category_id);
        $sql = "SELECT * FROM brands WHERE category_id = '$category_id' ORDER BY name";
        $result = mysqli_query($con, $sql);
        $list = array();
        while($row = mysqli_fetch_assoc($result)){
            array_push($list, $row);
        }
        return $list;
    }

    function getBrandById($id){
        $con = getConnection();
        $id = mysqli_real_escape_string($con, $id);
        $sql = "SELECT * FROM brands WHERE id = '$id'";
        $result = mysqli_query($con, $sql);
        return mysqli_fetch_assoc($result);
    }

    function addBrand($name, $category_id){
        $con = getConnection();
        $name = mysqli_real_escape_string($con, $name);
        $category_id = mysqli_real_escape_string($con, $category_id);
        $sql = "INSERT INTO brands (name, category_id) VALUES ('$name', '$category_id')";
        return mysqli_query($con, $sql);
    }

    function updateBrand($id, $name, $category_id){
        $con = getConnection();
        $id = mysqli_real_escape_string($con, $id);
        $name = mysqli_real_escape_string($con, $name);
        $category_id = mysqli_real_escape_string($con, $category_id);
        $sql = "UPDATE brands SET name = '$name', category_id = '$category_id' WHERE id = '$id'";
        return mysqli_query($con, $sql);
    }

    function brandHasProducts($id){
        $con = getConnection();
        $id = mysqli_real_escape_string($con, $id);
        $sql = "SELECT COUNT(*) AS c FROM products WHERE brand_id = '$id'";
        $result = mysqli_query($con, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['c'] > 0;
    }

    function deleteBrand($id){
        $con = getConnection();
        $id = mysqli_real_escape_string($con, $id);
        $sql = "DELETE FROM brands WHERE id = '$id'";
        return mysqli_query($con, $sql);
    }

    function countBrands(){
        $con = getConnection();
        $result = mysqli_query($con, "SELECT COUNT(*) AS c FROM brands");
        $row = mysqli_fetch_assoc($result);
        return $row['c'];
    }
?>
