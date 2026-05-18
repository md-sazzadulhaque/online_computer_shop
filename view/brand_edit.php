<?php
    require_once('../config/auth.php');
    require_once('../model/brandModel.php');
    require_once('../model/categoryModel.php');

    $id = isset($_GET['id']) ? $_GET['id'] : 0;
    $brand = getBrandById($id);
    if(!$brand){
        $_SESSION['msg'] = "Brand not found.";
        header('location: brand_list.php');
        exit;
    }
    $categories = getAllCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Brand</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('_navbar.php'); ?>
    <div class="container">
        <h1>Edit Brand</h1>

        <form method="post" action="../controller/brandController.php?action=edit"
              onsubmit="return validateBrandForm();">
            <fieldset>
                <legend>Brand details</legend>
                <p id="msg" class="error" style="display:none"></p>

                <input type="hidden" name="id" value="<?php echo $brand['id']; ?>" />

                Name:
                <input type="text" name="name" id="name"
                       value="<?php echo e($brand['name']); ?>" maxlength="100" />

                Category:
                <select name="category_id" id="category_id">
                    <option value="">-- Select Category --</option>
                    <?php foreach($categories as $c){
                        $sel = "";
                        if($brand['category_id'] == $c['id']){
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

                <input type="submit" name="submit" value="Update Brand" />
            </fieldset>
        </form>
    </div>
    <script src="../js/validate.js"></script>
</body>
</html>
