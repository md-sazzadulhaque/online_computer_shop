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