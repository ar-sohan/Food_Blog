<?php

    $host ="127.0.0.1";
    $dbname = "online_food_blog";
    $dbuser = "root";
    $dbpass = "";


    function getConnection(){
        global $host;
        global $dbname;
        $con = mysqli_connect($host, $GLOBALS['dbuser'], $GLOBALS['dbpass'], $dbname);
        if (!$con) {
            die ("Connection error: " . mysqli_connect_error());
        }
        return $con;
    }


?>