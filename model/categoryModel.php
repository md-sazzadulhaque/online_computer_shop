<?php
    require_once('../config/db.php');

    // ---- READ ----
    function getAllCategories(){
        $con = getConnection();
        $sql = "SELECT c.*, p.name AS parent_name
                FROM categories c
                LEFT JOIN categories p ON c.parent_id = p.id
                ORDER BY c.name";
        $result = mysqli_query($con, $sql);
        $list = array();
        while($row = mysqli_fetch_assoc($result)){
            array_push($list, $row);
        }
        return $list;
    }

    function getTopLevelCategories(){
        $con = getConnection();
        $sql = "SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name";
        $result = mysqli_query($con, $sql);
        $list = array();
        while($row = mysqli_fetch_assoc($result)){
            array_push($list, $row);
        }
        return $list;
    }

    function getCategoryById($id){
        $con = getConnection();
        $id = mysqli_real_escape_string($con, $id);
        $sql = "SELECT * FROM categories WHERE id = '$id'";
        $result = mysqli_query($con, $sql);
        return mysqli_fetch_assoc($result);
    }

    // ---- CREATE ----
    function addCategory($name, $parent_id){
        $con = getConnection();
        $name = mysqli_real_escape_string($con, $name);
        if($parent_id == "" || $parent_id == null){
            $sql = "INSERT INTO categories (name, parent_id) VALUES ('$name', NULL)";
        }else{
            $parent_id = mysqli_real_escape_string($con, $parent_id);
            $sql = "INSERT INTO categories (name, parent_id) VALUES ('$name', '$parent_id')";
        }
        return mysqli_query($con, $sql);
    }

    // ---- UPDATE ----
    function updateCategory($id, $name, $parent_id){
        $con = getConnection();
        $id = mysqli_real_escape_string($con, $id);
        $name = mysqli_real_escape_string($con, $name);
        if($parent_id == "" || $parent_id == null){
            $sql = "UPDATE categories SET name = '$name', parent_id = NULL WHERE id = '$id'";
        }else{
            $parent_id = mysqli_real_escape_string($con, $parent_id);
            $sql = "UPDATE categories SET name = '$name', parent_id = '$parent_id' WHERE id = '$id'";
        }
        return mysqli_query($con, $sql);
    }

    // ---- DELETE checks ----
    function categoryHasChildren($id){
        $con = getConnection();
        $id = mysqli_real_escape_string($con, $id);
        $sql = "SELECT COUNT(*) AS c FROM categories WHERE parent_id = '$id'";
        $result = mysqli_query($con, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['c'] > 0;
    }

    function categoryHasBrands($id){
        $con = getConnection();
        $id = mysqli_real_escape_string($con, $id);
        $sql = "SELECT COUNT(*) AS c FROM brands WHERE category_id = '$id'";
        $result = mysqli_query($con, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['c'] > 0;
    }

    function categoryHasProducts($id){
        $con = getConnection();
        $id = mysqli_real_escape_string($con, $id);
        $sql = "SELECT COUNT(*) AS c FROM products WHERE category_id = '$id'";
        $result = mysqli_query($con, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['c'] > 0;
    }

    function deleteCategory($id){
        $con = getConnection();
        $id = mysqli_real_escape_string($con, $id);
        $sql = "DELETE FROM categories WHERE id = '$id'";
        return mysqli_query($con, $sql);
    }

    function countCategories(){
        $con = getConnection();
        $result = mysqli_query($con, "SELECT COUNT(*) AS c FROM categories");
        $row = mysqli_fetch_assoc($result);
        return $row['c'];
    }
?>
