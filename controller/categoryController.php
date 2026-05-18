<?php
    require_once('../config/auth.php');
    require_once('../model/categoryModel.php');

    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

    // ---- ADD ----
    if($action == 'add' && isset($_REQUEST['submit'])){
        $name      = trim($_REQUEST['name']);
        $parent_id = $_REQUEST['parent_id'];

        $errors = array();
        if($name == ""){
            array_push($errors, "Category name is required.");
        }else if(strlen($name) > 100){
            array_push($errors, "Category name too long (max 100).");
        }

        if(count($errors) > 0){
            $_SESSION['errors'] = $errors;
            header('location: ../view/category_add.php');
            exit;
        }

        addCategory($name, $parent_id);
        $_SESSION['msg'] = "Category added successfully.";
        header('location: ../view/category_list.php');
        exit;
    }

    // ---- EDIT ----
    if($action == 'edit' && isset($_REQUEST['submit'])){
        $id        = $_REQUEST['id'];
        $name      = trim($_REQUEST['name']);
        $parent_id = $_REQUEST['parent_id'];

        $errors = array();
        if($name == ""){
            array_push($errors, "Category name is required.");
        }
        
        if($parent_id != "" && $parent_id == $id){
            array_push($errors, "A category cannot be its own parent.");
        }

        if(count($errors) > 0){
            $_SESSION['errors'] = $errors;
            header("location: ../view/category_edit.php?id=$id");
            exit;
        }

        updateCategory($id, $name, $parent_id);
        $_SESSION['msg'] = "Category updated.";
        header('location: ../view/category_list.php');
        exit;
    }

    // ---- DELETE ----
    if($action == 'delete'){
        $id = $_REQUEST['id'];

        if(categoryHasChildren($id)){
            $_SESSION['msg'] = "Cannot delete: this category has sub-categories.";
        }else if(categoryHasBrands($id)){
            $_SESSION['msg'] = "Cannot delete: this category has brands under it.";
        }else if(categoryHasProducts($id)){
            $_SESSION['msg'] = "Cannot delete: this category has products under it.";
        }else{
            deleteCategory($id);
            $_SESSION['msg'] = "Category deleted.";
        }
        header('location: ../view/category_list.php');
        exit;
    }

    header('location: ../view/category_list.php');
?>
