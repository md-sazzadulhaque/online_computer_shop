<?php
    require_once('../config/auth.php');
    require_once('../model/categoryModel.php');
    require_once('../model/brandModel.php');
    require_once('../model/productModel.php');

    $totalProducts   = countProducts();
    $totalCategories = countCategories();
    $totalBrands     = countBrands();
    $lowStock        = getLowStockProducts(5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('_navbar.php'); ?>

    <div class="container">
        <h1>Admin Dashboard</h1>

        <div class="cards">
            <div class="card">
                <h3>Total Products</h3>
                <div class="num"><?php echo $totalProducts; ?></div>
            </div>
            <div class="card">
                <h3>Total Categories</h3>
                <div class="num"><?php echo $totalCategories; ?></div>
            </div>
            <div class="card">
                <h3>Total Brands</h3>
                <div class="num"><?php echo $totalBrands; ?></div>
            </div>
            <div class="card">
                <h3>Low-Stock Alerts (less than 5)</h3>
                <div class="num"><?php echo count($lowStock); ?></div>
            </div>
        </div>

        <h2>Low-Stock Products</h2>
        <?php if(count($lowStock) == 0){ ?>
            <p>All products have sufficient stock.</p>
        <?php }else{ ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Stock</th>
                    <th>Price (BDT)</th>
                </tr>
                <?php foreach($lowStock as $p){ ?>
                <tr>
                    <td><?php echo $p['id']; ?></td>
                    <td><?php echo e($p['name']); ?></td>
                    <td><?php echo $p['stock']; ?> pcs</td>
                    <td>Tk. <?php echo $p['price']; ?></td>
                </tr>
                <?php } ?>
            </table>
        <?php } ?>
    </div>
</body>
</html>
