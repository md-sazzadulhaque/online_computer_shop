<?php
require_once('../db.php');

function registerCustomer($full_name, $username, $password){
    $con = getConnection();

    $full_name = mysqli_real_escape_string($con, $full_name);
    $username  = mysqli_real_escape_string($con, $username);
    $password  = mysqli_real_escape_string($con, $password);

    // Check username already exists
    $check = "SELECT * FROM customers WHERE username='$username'";
    $result = mysqli_query($con, $check);

    if($result && mysqli_num_rows($result) > 0){
        return "Username already exists";
    }

    $sql = "INSERT INTO customers(full_name, username, password)
            VALUES('$full_name', '$username', '$password')";

    if(mysqli_query($con, $sql)){
        return true;
    }

    return "Registration failed";
}

<?php
require_once('../db.php');

function registerCustomer($full_name, $username, $password){
    $con = getConnection();

    $full_name = mysqli_real_escape_string($con, $full_name);
    $username  = mysqli_real_escape_string($con, $username);
    $password  = mysqli_real_escape_string($con, $password);

    // Check username already exists
    $check = "SELECT * FROM customers WHERE username='$username'";
    $result = mysqli_query($con, $check);

    if($result && mysqli_num_rows($result) > 0){
        return "Username already exists";
    }

    $sql = "INSERT INTO customers(full_name, username, password)
            VALUES('$full_name', '$username', '$password')";

    if(mysqli_query($con, $sql)){
        return true;
    }

    return "Registration failed";
}

function loginCustomer($username, $password){
    $con = getConnection();

    $username = mysqli_real_escape_string($con, $username);
    $password = mysqli_real_escape_string($con, $password);

    $sql = "SELECT * FROM customers WHERE username='$username' AND password='$password'";
    $result = mysqli_query($con, $sql);

    if($result && mysqli_num_rows($result) == 1){
        return mysqli_fetch_assoc($result);
    }

    return false;
}

