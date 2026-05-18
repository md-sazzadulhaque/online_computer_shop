<?php
    // Entry point - send admin to dashboard, otherwise go to Task 1 login.
    session_start();

    if(isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin'){
        header('location: view/dashboard.php');
    }else{
        header('location: view/login.php');
    }
?>
