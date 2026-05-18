<?php
    // Navbar + flash messages. Include AFTER session_start() / auth gate.
?>
<div class="navbar">
    <a href="dashboard.php">Admin Dashboard</a>
    <a href="category_list.php">Categories</a>
    <a href="brand_list.php">Brands</a>
    <a href="product_list.php">Products</a>
    <a href="../controller/logout.php" style="float:right">
        Logout (<?php echo e($_SESSION['name']); ?>)
    </a>
</div>

<?php if(isset($_SESSION['msg'])){ ?>
    <div class="container">
        <div class="msg"><?php echo e($_SESSION['msg']); ?></div>
    </div>
    <?php unset($_SESSION['msg']); ?>
<?php } ?>

<?php if(isset($_SESSION['errors']) && count($_SESSION['errors']) > 0){ ?>
    <div class="container">
        <div class="error">
            <strong>Please fix the following:</strong>
            <ul>
            <?php foreach($_SESSION['errors'] as $err){ ?>
                <li><?php echo e($err); ?></li>
            <?php } ?>
            </ul>
        </div>
    </div>
    <?php unset($_SESSION['errors']); ?>
<?php } ?>
