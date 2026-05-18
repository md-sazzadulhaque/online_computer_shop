<?php
    require_once('../config/auth.php');
    require_once('../model/productModel.php');

    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

    function handleImageUpload(&$errors){
        if(!isset($_FILES['image']) || $_FILES['image']['name'] == ""){
            return "";
        }

        $file = $_FILES['image'];

        if($file['error'] != 0){
            array_push($errors, "Image upload failed.");
            return "";
        }
        // size check: max 2 MB
        if($file['size'] > 2 * 1024 * 1024){
            array_push($errors, "Image must be 2MB or less.");
            return "";
        }
        // type check (only JPEG / PNG allowed)
        $type = $file['type'];
        if($type == "image/jpeg"){
            $ext = "jpg";
        }else if($type == "image/png"){
            $ext = "png";
        }else{
            array_push($errors, "Only JPEG or PNG images are allowed.");
            return "";
        }

        $newName = 'p_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        $relPath = 'public/uploads/products/' . $newName;
        $destination = '../' . $relPath;

        if(!move_uploaded_file($file['tmp_name'], $destination)){
            array_push($errors, "Could not save uploaded image.");
            return "";
        }
        return $relPath;
    }

    // ---- ADD ----
    if($action == 'add' && isset($_REQUEST['submit'])){
        $name        = trim($_REQUEST['name']);
        $description = trim($_REQUEST['description']);
        $manuf       = trim($_REQUEST['manufacturer_review']);
        $price       = $_REQUEST['price'];
        $category_id = $_REQUEST['category_id'];
        $brand_id    = $_REQUEST['brand_id'];
        $stock       = $_REQUEST['stock'];

        $errors = array();
        if($name == ""){
            array_push($errors, "Product name is required.");
        }
        if($category_id == ""){
            array_push($errors, "Category is required.");
        }
        if($brand_id == ""){
            array_push($errors, "Brand is required.");
        }
        if(!is_numeric($price) || $price <= 0){
            array_push($errors, "Price must be a positive number.");
        }
        if(!is_numeric($stock) || $stock < 0){
            array_push($errors, "Stock must be a non-negative number.");
        }

        $imagePath = handleImageUpload($errors);

        if(count($errors) > 0){
            $_SESSION['errors'] = $errors;
            header('location: ../view/product_add.php');
            exit;
        }

        $product = array(
            'name'                => $name,
            'description'         => $description,
            'manufacturer_review' => $manuf,
            'price'               => $price,
            'category_id'         => $category_id,
            'brand_id'            => $brand_id,
            'image_path'          => $imagePath,
            'stock'               => $stock
        );
        addProduct($product);
        $_SESSION['msg'] = "Product added.";
        header('location: ../view/product_list.php');
        exit;
    }

    // ---- EDIT ----
    if($action == 'edit' && isset($_REQUEST['submit'])){
        $id          = $_REQUEST['id'];
        $name        = trim($_REQUEST['name']);
        $description = trim($_REQUEST['description']);
        $manuf       = trim($_REQUEST['manufacturer_review']);
        $price       = $_REQUEST['price'];
        $category_id = $_REQUEST['category_id'];
        $brand_id    = $_REQUEST['brand_id'];
        $stock       = $_REQUEST['stock'];

        $errors = array();
        if($name == ""){
            array_push($errors, "Product name is required.");
        }
        if(!is_numeric($price) || $price <= 0){
            array_push($errors, "Price must be a positive number.");
        }
        if(!is_numeric($stock) || $stock < 0){
            array_push($errors, "Stock must be a non-negative number.");
        }

        $existing = getProductById($id);
        if(!$existing){
            $_SESSION['msg'] = "Product not found.";
            header('location: ../view/product_list.php');
            exit;
        }

        $newImg = handleImageUpload($errors);

        if(count($errors) > 0){
            $_SESSION['errors'] = $errors;
            header("location: ../view/product_edit.php?id=$id");
            exit;
        }

        $imagePath = $existing['image_path'];
        if($newImg != ""){
            if($existing['image_path'] != ""){
                $oldPath = '../' . $existing['image_path'];
                if(file_exists($oldPath)){
                    unlink($oldPath);
                }
            }
            $imagePath = $newImg;
        }

        $product = array(
            'id'                  => $id,
            'name'                => $name,
            'description'         => $description,
            'manufacturer_review' => $manuf,
            'price'               => $price,
            'category_id'         => $category_id,
            'brand_id'            => $brand_id,
            'image_path'          => $imagePath,
            'stock'               => $stock
        );
        updateProduct($product);
        $_SESSION['msg'] = "Product updated.";
        header('location: ../view/product_list.php');
        exit;
    }

    // ---- DELETE ----
    if($action == 'delete'){
        $id = $_REQUEST['id'];
        deleteProduct($id);
        $_SESSION['msg'] = "Product deleted.";
        header('location: ../view/product_list.php');
        exit;
    }

    header('location: ../view/product_list.php');
?>
