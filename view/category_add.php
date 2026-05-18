<?php
    require_once('../config/auth.php');
    require_once('../model/categoryModel.php');
    $parents = getTopLevelCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Category</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('_navbar.php'); ?>
    <div class="container">
        <h1>Add Category</h1>

        <form method="post" action="../controller/categoryController.php?action=add"
              onsubmit="return validateCategoryForm();">
            <fieldset>
                <legend>Category details</legend>
                <p id="msg" class="error" style="display:none"></p>

                Name:
                <input type="text" name="name" id="name" value="" maxlength="100" />

                Parent Category (optional):
                <select name="parent_id" id="parent_id">
                    <option value="">-- None (top-level) --</option>
                    <?php foreach($parents as $p){ ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo e($p['name']); ?></option>
                    <?php } ?>
                </select>

                <input type="submit" name="submit" value="Add Category" />
            </fieldset>
        </form>
    </div>
    <script src="../js/validate.js"></script>
</body>
</html>
