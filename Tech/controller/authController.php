<?php
session_start();
require_once('../model/customerModel.php');

// REGISTER
if(isset($_POST['register'])){
    $full_name = $_POST['full_name'];
    $username  = $_POST['username'];
    $password  = $_POST['password'];

    if($full_name == "" || $username == "" || $password == ""){
        header('location: ../view/register.php?error=All fields are required');
        exit;
    }

    $result = registerCustomer($full_name, $username, $password);

    if($result === true){
        header('location: ../view/login.php?success=Registration successful. Please login.');
    } else {
        header('location: ../view/register.php?error=' . urlencode($result));
    }
    exit;
}
// LOGIN
if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    if($username == "" || $password == ""){
        header('location: ../view/login.php?error=All fields are required');
        exit;
    }

    $customer = loginCustomer($username, $password);

    if($customer){
        $_SESSION['customer_id']   = $customer['id'];
        $_SESSION['customer_name'] = $customer['full_name'];
        $_SESSION['username']      = $customer['username'];
        header('location: ../view/home.php');
    } else {
        header('location: ../view/login.php?error=Invalid username or password');
    }
    exit;
}

// LOGOUT
if(isset($_GET['logout'])){
    session_destroy();
    header('location: ../view/login.php?success=Logged out successfully');
    exit;
}
