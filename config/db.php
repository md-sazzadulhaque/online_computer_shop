<?php
    function getConnection(){
        $con = mysqli_connect("localhost", "root", "", "computer_shop5");
        if(!$con){
            die("Connection failed: " . mysqli_connect_error());
        }
        return $con;
    }
?>
