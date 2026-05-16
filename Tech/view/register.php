<?php
session_start();
if(isset($_SESSION['customer_id'])){
    header('location: home.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - TechShop</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background-color: #f0f0f0; color: #222; }
        a { text-decoration: none; color: #222; }

        .navbar {
            background-color: #222;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .navbar .logo { color: white; font-size: 20px; font-weight: bold; margin-right: 10px; }
        .navbar a { color: #ccc; font-size: 14px; }
        .navbar a:hover { color: white; }

        .container {
            width: 400px;
            margin: 60px auto;
            background: white;
            border: 1px solid #ddd;
            padding: 30px;
        }

        h2 { font-size: 20px; margin-bottom: 20px; color: #222; }

        .error {
            color: red;
            background-color: #ffe6e6;
            padding: 8px 12px;
            border: 1px solid red;
            margin-bottom: 14px;
            font-size: 14px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 5px;
            margin-top: 14px;
            color: #555;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 9px 10px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #222;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 15px;
            margin-top: 20px;
        }
        input[type="submit"]:hover { background-color: #444; }

        .bottom-link { font-size: 13px; margin-top: 14px; color: #666; }
        .bottom-link a { color: #222; text-decoration: underline; }
    </style>
</head>
<body>

<div class="navbar">
    <span class="logo">TechShop</span>
    <a href="home.php">Home</a>
    <a href="login.php">Login</a>
</div>

<div class="container">

    <h2>Create Account</h2>

    <?php if(isset($_GET['error'])): ?>
        <div class="error"><?php echo $_GET['error']; ?></div>
    <?php endif; ?>

    <form method="post" action="../controller/authController.php">

        <label>Full Name</label>
        <input type="text" name="full_name" placeholder="Your full name">

        <label>Username</label>
        <input type="text" name="username" placeholder="Choose a username">

        <label>Password</label>
        <input type="password" name="password" placeholder="Choose a password">

        <input type="submit" name="register" value="Register">

    </form>

    <p class="bottom-link">Already have an account? <a href="login.php">Login here</a></p>
         <p class="bottom-link"> <a href="home.php">BACK</a></p>


</div>
</body>
</html>
