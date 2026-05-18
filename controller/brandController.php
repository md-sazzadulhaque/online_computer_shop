<?php
    require_once('../config/auth.php');
    require_once('../model/brandModel.php');

    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

    // ---- ADD ----
    if($action == 'add' && isset($_REQUEST['submit'])){
        $name        = trim($_REQUEST['name']);
        $category_id = $_REQUEST['category_id'];

        $errors = array();
        if($name == ""){
            array_push($errors, "Brand name is required.");
        }
        if($category_id == ""){
            array_push($errors, "Please select a category.");
        }

        if(count($errors) > 0){
            $_SESSION['errors'] = $errors;
            header('location: ../view/brand_add.php');
            exit;
        }

        addBrand($name, $category_id);
        $_SESSION['msg'] = "Brand added.";
        header('location: ../view/brand_list.php');
        exit;
    }

    // ---- EDIT ----
    if($action == 'edit' && isset($_REQUEST['submit'])){
        $id          = $_REQUEST['id'];
        $name        = trim($_REQUEST['name']);
        $category_id = $_REQUEST['category_id'];

        $errors = array();
        if($name == ""){
            array_push($errors, "Brand name is required.");
        }
        if($category_id == ""){
            array_push($errors, "Please select a category.");
        }

        if(count($errors) > 0){
            $_SESSION['errors'] = $errors;
            header("location: ../view/brand_edit.php?id=$id");
            exit;
        }

        updateBrand($id, $name, $category_id);
        $_SESSION['msg'] = "Brand updated.";
        header('location: ../view/brand_list.php');
        exit;
    }

    // ---- DELETE ----
    if($action == 'delete'){
        $id = $_REQUEST['id'];
        if(brandHasProducts($id)){
            $_SESSION['msg'] = "Cannot delete: this brand has products.";
        }else{
            deleteBrand($id);
            $_SESSION['msg'] = "Brand deleted.";
        }
        header('location: ../view/brand_list.php');
        exit;
    }

    header('location: ../view/brand_list.php');
?>
