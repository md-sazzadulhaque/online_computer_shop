<?php
    require_once('../config/auth.php');
    require_once('../model/brandModel.php');
    $brands = getAllBrands();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Brands</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('_navbar.php'); ?>
    <div class="container">
        <h1>Brand Management</h1>
        <p><a class="btn" href="brand_add.php">+ Add Brand</a></p>

        <table>
            <tr>
                <th>ID</th>
                <th>Brand</th>
                <th>Category</th>
                <th>Action</th>
            </tr>
            <?php foreach($brands as $b){ ?>
            <tr>
                <td><?php echo $b['id']; ?></td>
                <td><?php echo e($b['name']); ?></td>
                <td><?php echo e($b['category_name']); ?></td>
                <td>
                    <a class="btn" href="brand_edit.php?id=<?php echo $b['id']; ?>">Edit</a>
                    <a class="btn btn-danger"
                       href="../controller/brandController.php?action=delete&id=<?php echo $b['id']; ?>"
                       onclick="return confirm('Delete this brand?');">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
