<?php
session_start();

require_once(__DIR__ . '/../config/db.php');

if(isset($_GET['logout'])){
    session_destroy();
    header('Location: ../view/login.php?success=You have been logged out');
    exit;
}


if(isset($_POST['login'])){
    $email    = isset($_POST['email'])    ? trim($_POST['email'])    : '';
    $password = isset($_POST['password']) ? $_POST['password']       : '';

   
    if($email === '' || $password === ''){
        header('Location: ../view/login.php?error=All fields are required');
        exit;
    }

    $pdo  = getDB();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    $valid = false;
    if($user){
        if(password_verify($password, $user['password'])){
            $valid = true;
        } elseif($user['password'] === $password){
            // Fallback for plain text passwords in shared schema sample data
            $valid = true;
        }
    }

    if($valid){
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        header('Location: ../view/home.php?success=Welcome back, ' . urlencode($user['name']));
    } else {
        header('Location: ../view/login.php?error=Invalid email or password');
    }
    exit;
}

if(isset($_POST['register'])){
    $name     = isset($_POST['name'])     ? trim($_POST['name'])     : '';
    $email    = isset($_POST['email'])    ? trim($_POST['email'])    : '';
    $password = isset($_POST['password']) ? $_POST['password']       : '';
    $confirm  = isset($_POST['confirm'])  ? $_POST['confirm']        : '';

    if($name === '' || $email === '' || $password === '' || $confirm === ''){
        header('Location: ../view/register.php?error=All fields are required');
        exit;
    }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        header('Location: ../view/register.php?error=Invalid email format');
        exit;
    }
    if(strlen($password) < 8){
        header('Location: ../view/register.php?error=Password must be at least 8 characters');
        exit;
    }
    if($password !== $confirm){
        header('Location: ../view/register.php?error=Passwords do not match');
        exit;
    }

    $pdo = getDB();

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if($stmt->fetch()){
        header('Location: ../view/register.php?error=Email already registered');
        exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare(
        "INSERT INTO users(name, email, password, role) VALUES(?,?,?,'customer')"
    );
    $stmt->execute([$name, $email, $hashed]);

    header('Location: ../view/login.php?success=Registration successful. Please login.');
    exit;
}
