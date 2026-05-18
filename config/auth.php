<?php
    session_start();

    if(!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
        header('location: ../view/login.php');
        exit;
    }
    function e($str){
        return htmlspecialchars($str);
    }
?>
