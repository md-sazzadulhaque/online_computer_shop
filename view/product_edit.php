<?php
    require_once('../config/auth.php');
    require_once('../model/productModel.php');
    require_once('../model/categoryModel.php');
    require_once('../model/brandModel.php');

    $id = isset($_GET['id']) ? $_GET['id'] : 0;
    $product = getProductById($id);
    if(!$product){
        $_SESSION['msg'] = "Product not found.";
        header('location: product_list.php');
        exit;
    }
    $categories = getAllCategories();
    $brandsForCat = getBrandsByCategory($product['category_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('_navbar.php'); ?>
    <div class="container">
        <h1>Edit Product</h1>

        <form method="post" action="../controller/productController.php?action=edit"
              enctype="multipart/form-data"
              onsubmit="return validateProductForm();">
            <fieldset>
                <legend>Product details</legend>
                <p id="msg" class="error" style="display:none"></p>

                <input type="hidden" name="id" value="<?php echo $product['id']; ?>" />

                Name:
                <input type="text" name="name" id="name"
                       value="<?php echo e($product['name']); ?>" maxlength="150" />

                Description:
                <textarea name="description"><?php echo e($product['description']); ?></textarea>

                Manufacturer Review:
                <textarea name="manufacturer_review"><?php echo e($product['manufacturer_review']); ?></textarea>

                Price:
                <input type="text" name="price" id="price"
                       value="<?php echo $product['price']; ?>" />

                Stock:
                <input type="text" name="stock" id="stock"
                       value="<?php echo $product['stock']; ?>" />

                Category:
                <select name="category_id" id="category_id" onchange="loadBrandsByCategory();">
                    <option value="">-- Select Category --</option>
                    <?php foreach($categories as $c){
                        $sel = "";
                        if($product['category_id'] == $c['id']){
                            $sel = "selected";
                        }
                    ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo $sel; ?>>
                            <?php echo e($c['name']); ?>
                            <?php if($c['parent_name'] != ""){
                                echo "(" . e($c['parent_name']) . ")";
                            } ?>
                        </option>
                    <?php } ?>
                </select>

                Brand:
                <select name="brand_id" id="brand_id">
                    <option value="">-- Select Brand --</option>
                    <?php foreach($brandsForCat as $b){
                        $sel = "";
                        if($product['brand_id'] == $b['id']){
                            $sel = "selected";
                        }
                    ?>
                        <option value="<?php echo $b['id']; ?>" <?php echo $sel; ?>>
                            <?php echo e($b['name']); ?>
                        </option>
                    <?php } ?>
                </select>

                Current Image:
                <?php if($product['image_path'] != ""){ ?>
                    <br>
                    <img class="product-thumb" style="width:120px"
                         src="../<?php echo e($product['image_path']); ?>" />
                    <br>
                <?php }else{ ?>
                    <i>(none)</i><br>
                <?php } ?>

                Replace Image (optional, JPEG/PNG, max 2MB):
                <input type="file" name="image" id="image" accept="image/jpeg,image/png" />

                <input type="submit" name="submit" value="Update Product" />
            </fieldset>
        </form>
    </div>
    <script src="../js/validate.js"></script>
</body>
</html>
