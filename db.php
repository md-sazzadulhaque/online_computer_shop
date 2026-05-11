<?php
function getConnection(){
    $con = mysqli_connect("localhost", "root", "", "shop_db");
    if(!$con){
        die("Connection failed: " . mysqli_connect_error());
    }
    return $con;
}
