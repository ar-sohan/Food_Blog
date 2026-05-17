<?php

    $host   = "127.0.0.1";
    $dbname = "food_blog";
    $dbuser = "root";
    $dbpass = "";

    function getConnection(){
        global $host, $dbname, $dbuser, $dbpass;
        $con = mysqli_connect($host, $dbuser, $dbpass, $dbname);
        if(!$con){
            die("DB connection error: ".mysqli_connect_error());
        }
        mysqli_set_charset($con, "utf8mb4");
        return $con;
    }

?>