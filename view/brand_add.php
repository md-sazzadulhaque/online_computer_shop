<?php
    require_once('../config/auth.php');
    require_once('../model/categoryModel.php');
    $categories = getAllCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Brand</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('_navbar.php'); ?>
    <div class="container">
        <h1>Add Brand</h1>

        <form method="post" action="../controller/brandController.php?action=add"
              onsubmit="return validateBrandForm();">
            <fieldset>
                <legend>Brand details</legend>
                <p id="msg" class="error" style="display:none"></p>

                Name:
                <input type="text" name="name" id="name" value="" maxlength="100" />

                Category:
                <select name="category_id" id="category_id">
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

                <input type="submit" name="submit" value="Add Brand" />
            </fieldset>
        </form>
    </div>
    <script src="../js/validate.js"></script>
</body>
</html>
