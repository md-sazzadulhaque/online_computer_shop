<?php
    require_once('../config/auth.php');
    require_once('../model/productModel.php');
    $products = getAllProducts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Products</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('_navbar.php'); ?>
    <div class="container">
        <h1>Product Management</h1>
        <p><a class="btn" href="product_add.php">+ Add Product</a></p>

        <table>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Price (BDT)</th>
                <th>Stock</th>
                <th>Action</th>
            </tr>
            <?php foreach($products as $p){ ?>
            <tr>
                <td><?php echo $p['id']; ?></td>
                <td>
                    <?php if($p['image_path'] != ""){ ?>
                        <img class="product-thumb" src="../<?php echo e($p['image_path']); ?>" alt="" />
                    <?php }else{ ?>
                        <i>no image</i>
                    <?php } ?>
                </td>
                <td><?php echo e($p['name']); ?></td>
                <td><?php echo e($p['category_name']); ?></td>
                <td><?php echo e($p['brand_name']); ?></td>
                <td>Tk. <?php echo $p['price']; ?></td>
                <td><?php echo $p['stock']; ?> pcs</td>
                <td>
                    <a class="btn" href="product_edit.php?id=<?php echo $p['id']; ?>">Edit</a>
                    <a class="btn btn-danger"
                       href="../controller/productController.php?action=delete&id=<?php echo $p['id']; ?>"
                       onclick="return confirm('Delete this product?');">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
