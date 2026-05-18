<?php
    require_once('../config/auth.php');
    require_once('../model/categoryModel.php');
    $categories = getAllCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Product</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('_navbar.php'); ?>
    <div class="container">
        <h1>Add Product</h1>

        <form method="post" action="../controller/productController.php?action=add"
              enctype="multipart/form-data"
              onsubmit="return validateProductForm();">
            <fieldset>
                <legend>Product details</legend>
                <p id="msg" class="error" style="display:none"></p>

                Name:
                <input type="text" name="name" id="name" value="" maxlength="150" />

                Description:
                <textarea name="description"></textarea>

                Manufacturer Review:
                <textarea name="manufacturer_review"></textarea>

                Price:
                <input type="text" name="price" id="price" value="" />

                Stock:
                <input type="text" name="stock" id="stock" value="0" />

                Category:
                <select name="category_id" id="category_id" onchange="loadBrandsByCategory();">
                    <option value="">-- Select Category --</option>
                    <?php foreach($categories as $c){ ?>
                        <option value="<?php echo $c['id']; ?>">
                            <?php echo e($c['name']); ?>
                            <?php if($c['parent_name'] != ""){
                                echo "(" . e($c['parent_name']) . ")";
                            } ?>
                        </option>
                    <?php } ?>
                </select>

                Brand (loads via AJAX after category selected):
                <select name="brand_id" id="brand_id">
                    <option value="">-- Select Category First --</option>
                </select>

                Image (JPEG/PNG, max 2MB):
                <input type="file" name="image" id="image" accept="image/jpeg,image/png" />

                <input type="submit" name="submit" value="Add Product" />
            </fieldset>
        </form>
    </div>
    <script src="../js/validate.js"></script>
</body>
</html>
