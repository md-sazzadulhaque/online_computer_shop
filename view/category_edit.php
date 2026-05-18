<?php
    require_once('../config/auth.php');
    require_once('../model/categoryModel.php');

    $id = isset($_GET['id']) ? $_GET['id'] : 0;
    $cat = getCategoryById($id);
    if(!$cat){
        $_SESSION['msg'] = "Category not found.";
        header('location: category_list.php');
        exit;
    }
    $parents = getTopLevelCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Category</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('_navbar.php'); ?>
    <div class="container">
        <h1>Edit Category</h1>

        <form method="post" action="../controller/categoryController.php?action=edit"
              onsubmit="return validateCategoryForm();">
            <fieldset>
                <legend>Category details</legend>
                <p id="msg" class="error" style="display:none"></p>

                <input type="hidden" name="id" value="<?php echo $cat['id']; ?>" />

                Name:
                <input type="text" name="name" id="name"
                       value="<?php echo e($cat['name']); ?>" maxlength="100" />

                Parent Category:
                <select name="parent_id" id="parent_id">
                    <option value="">-- None (top-level) --</option>
                    <?php foreach($parents as $p){
                        // a category cannot be its own parent
                        if($p['id'] == $cat['id']){
                            continue;
                        }
                        $sel = "";
                        if($cat['parent_id'] == $p['id']){
                            $sel = "selected";
                        }
                    ?>
                        <option value="<?php echo $p['id']; ?>" <?php echo $sel; ?>>
                            <?php echo e($p['name']); ?>
                        </option>
                    <?php } ?>
                </select>

                <input type="submit" name="submit" value="Update Category" />
            </fieldset>
        </form>
    </div>
    <script src="../js/validate.js"></script>
</body>
</html>
