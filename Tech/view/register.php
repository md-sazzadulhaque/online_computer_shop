<?php
session_start();
if(isset($_SESSION['user_id'])){ header('Location: home.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - TechShop</title>
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
        .alert-error { padding:8px 12px; background:#ffe6e6; border:1px solid red; color:red; margin-bottom:14px; font-size:14px; }
        label { display:block; font-size:13px; font-weight:bold; color:#555; margin-bottom:5px; margin-top:14px; }
        input[type="text"], input[type="email"], input[type="password"] {
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
    <a href="login.php">Login</a>
</div>
<div class="box">
    <h2>Create Account</h2>

    <?php if(isset($_GET['error'])): ?>
        <div class="alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <form method="post" action="../controller/authController.php" id="reg-form">
        <label>Full Name</label>
        <input type="text" name="name" placeholder="Your full name">

        <label>Email</label>
        <input type="email" name="email" placeholder="your@email.com">

        <label>Password <small style="color:#aaa;">(min 8 characters)</small></label>
        <input type="password" name="password" placeholder="Choose a password">

        <label>Confirm Password</label>
        <input type="password" name="confirm" placeholder="Repeat password">

        <p id="js-error"></p>

        <input type="submit" name="register" value="Register">
    </form>

    <p class="bottom">Already have an account? <a href="login.php">Login here</a></p> <br>
        <p class="bottom"> <a href="home.php">BACK</a></p>

</div>

<script>
document.getElementById('reg-form').addEventListener('submit', function(e){
    var name    = this.name.value.trim();   // note: form field named "name"
    var email   = this.elements['email'].value.trim();
    var pass    = this.elements['password'].value;
    var confirm = this.elements['confirm'].value;
    var err     = document.getElementById('js-error');

    err.style.display = 'none';
    err.textContent   = '';

    if(!name || !email || !pass || !confirm){
        e.preventDefault();
        err.textContent = 'All fields are required';
        err.style.display = 'block';
        return;
    }

    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if(!emailRegex.test(email)){
        e.preventDefault();
        err.textContent = 'Please enter a valid email address';
        err.style.display = 'block';
        return;
    }

    if(pass.length < 8){
        e.preventDefault();
        err.textContent = 'Password must be at least 8 characters';
        err.style.display = 'block';
        return;
    }

    if(pass !== confirm){
        e.preventDefault();
        err.textContent = 'Passwords do not match';
        err.style.display = 'block';
        return;
    }
});
</script>
</body>
</html>
<?php
session_start();
if(isset($_SESSION['user_id'])){ header('Location: home.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - TechShop</title>
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
        .alert-error { padding:8px 12px; background:#ffe6e6; border:1px solid red; color:red; margin-bottom:14px; font-size:14px; }
        label { display:block; font-size:13px; font-weight:bold; color:#555; margin-bottom:5px; margin-top:14px; }
        input[type="text"], input[type="email"], input[type="password"] {
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
    <a href="login.php">Login</a>
</div>
<div class="box">
    <h2>Create Account</h2>

    <?php if(isset($_GET['error'])): ?>
        <div class="alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <form method="post" action="../controller/authController.php" id="reg-form">
        <label>Full Name</label>
        <input type="text" name="name" placeholder="Your full name">

        <label>Email</label>
        <input type="email" name="email" placeholder="your@email.com">

        <label>Password <small style="color:#aaa;">(min 8 characters)</small></label>
        <input type="password" name="password" placeholder="Choose a password">

        <label>Confirm Password</label>
        <input type="password" name="confirm" placeholder="Repeat password">

        <p id="js-error"></p>

        <input type="submit" name="register" value="Register">
    </form>

    <p class="bottom">Already have an account? <a href="login.php">Login here</a></p> <br>
        <p class="bottom"> <a href="home.php">BACK</a></p>

</div>

<script>
document.getElementById('reg-form').addEventListener('submit', function(e){
    var name    = this.name.value.trim();   // note: form field named "name"
    var email   = this.elements['email'].value.trim();
    var pass    = this.elements['password'].value;
    var confirm = this.elements['confirm'].value;
    var err     = document.getElementById('js-error');

    err.style.display = 'none';
    err.textContent   = '';

    if(!name || !email || !pass || !confirm){
        e.preventDefault();
        err.textContent = 'All fields are required';
        err.style.display = 'block';
        return;
    }

    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if(!emailRegex.test(email)){
        e.preventDefault();
        err.textContent = 'Please enter a valid email address';
        err.style.display = 'block';
        return;
    }

    if(pass.length < 8){
        e.preventDefault();
        err.textContent = 'Password must be at least 8 characters';
        err.style.display = 'block';
        return;
    }

    if(pass !== confirm){
        e.preventDefault();
        err.textContent = 'Passwords do not match';
        err.style.display = 'block';
        return;
    }
});
</script>
</body>
</html>
