<?php

$host = "localhost";
$dbname = "food_blog";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed!");
}
?>