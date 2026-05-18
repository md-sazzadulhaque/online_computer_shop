<?php
session_start();
if(isset($_SESSION['user_id'])){ header('Location: home.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - TechShop</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f0f0f0; }
        .navbar { background:#222; padding:12px 24px; display:flex; align-items:center; gap:20px; }
        .navbar .logo { color:white; font-size:20px; font-weight:bold; }
        .navbar a { color:#ccc; font-size:14px; text-decoration:none; }
        .navbar a:hover { color:white; }
        .box {
            width: 420px; margin: 60px auto;
            background: white; border: 1px solid #ddd; padding: 30px;
        }
        h2 { font-size:20px; margin-bottom:20px; }
        .alert-success { padding:8px 12px; background:#e6ffe6; border:1px solid green; color:green; margin-bottom:14px; font-size:14px; }
        .alert-error   { padding:8px 12px; background:#ffe6e6; border:1px solid red;   color:red;   margin-bottom:14px; font-size:14px; }
        label { display:block; font-size:13px; font-weight:bold; color:#555; margin-bottom:5px; margin-top:14px; }
        input[type="email"], input[type="password"] {
            width:100%; padding:9px 10px; border:1px solid #ccc; font-size:14px;
        }
        input[type="submit"] {
            width:100%; padding:10px; background:#222; color:white;
            border:none; cursor:pointer; font-size:15px; margin-top:20px;
        }
        input[type="submit"]:hover { background:#444; }
        .bottom { font-size:13px; margin-top:14px; color:#666; }
        .bottom a { color:#e67e00; }
        #js-error { color:red; font-size:13px; margin-top:8px; display:none; }
    </style>
</head>
<body>
<div class="navbar">
    <span class="logo">TechShop</span>
    <a href="home.php">Home</a>
    <a href="register.php">Register</a>
</div>
<div class="box">
    <h2>Customer Login</h2>

    <?php if(isset($_GET['success'])): ?>
        <div class="alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
    <?php endif; ?>
    <?php if(isset($_GET['error'])): ?>
        <div class="alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <form method="post" action="../controller/authController.php" id="login-form">
        <label>Email</label>
        <input type="email" name="email" placeholder="your@email.com">

        <label>Password</label>
        <input type="password" name="password" placeholder="Your password">

        <p id="js-error"></p>

        <input type="submit" name="login" value="Login">
    </form>

    <p class="bottom">No account? <a href="register.php">Register here</a></p> <br>
            <p class="bottom"> <a href="home.php">BACK</a></p>

</div>

<script>
document.getElementById('login-form').addEventListener('submit', function(e){
    var email = this.email.value.trim();
    var pass  = this.password.value;
    var err   = document.getElementById('js-error');
    err.style.display = 'none';

    if(email === '' || pass === ''){
        e.preventDefault();
        err.textContent   = 'Both fields are required';
        err.style.display = 'block';
    }
});
</script>
</body>
</html>
