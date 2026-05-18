<?php
    session_start();
    require_once('../config/db.php');

    $error = "";

    if(isset($_REQUEST['submit'])){
        $email    = trim($_REQUEST['email']);
        $password = $_REQUEST['password'];

        if($email == "" || $password == ""){
            $error = "Email and password are required.";
        }else{
            $con = getConnection();
            $email = mysqli_real_escape_string($con, $email);
            $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
            $result = mysqli_query($con, $sql);
            $user = mysqli_fetch_assoc($result);

            if($user){
                if($user['role'] == 'admin'){
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['name']    = $user['name'];
                    $_SESSION['role']    = $user['role'];
                    header('location: dashboard.php');
                    exit;
                }else{
                    $error = "This area is for admins only.";
                }
            }else{
                $error = "Invalid email or password.";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container" style="max-width:420px;margin-top:60px">
        <h1>Admin Login</h1>

        <?php if($error != ""){ ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php } ?>

        <form method="post" action="login.php">
            <fieldset>
                <legend>Sign in</legend>

                Email:
                <input type="email" name="email" value="" required />

                Password:
                <input type="password" name="password" value="" required />

                <input type="submit" name="submit" value="Login" />
            </fieldset>
        </form>

        <p><small>Default admin: <b>admin@shop.com</b> / <b>admin123</b></small></p>
    </div>
</body>
</html>
