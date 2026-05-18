<?php
    require_once('../config/auth.php');
    require_once('../model/categoryModel.php');
    $categories = getAllCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Categories</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('_navbar.php'); ?>
    <div class="container">
        <h1>Category Management</h1>
        <p><a class="btn" href="category_add.php">+ Add Category</a></p>

        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Parent Category</th>
                <th>Action</th>
            </tr>
            <?php foreach($categories as $c){ ?>
            <tr>
                <td><?php echo $c['id']; ?></td>
                <td><?php echo e($c['name']); ?></td>
                <td>
                    <?php if($c['parent_name'] != ""){
                        echo e($c['parent_name']);
                    }else{
                        echo "<i>(top-level)</i>";
                    } ?>
                </td>
                <td>
                    <a class="btn" href="category_edit.php?id=<?php echo $c['id']; ?>">Edit</a>
                    <a class="btn btn-danger"
                       href="../controller/categoryController.php?action=delete&id=<?php echo $c['id']; ?>"
                       onclick="return confirm('Delete this category?');">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
