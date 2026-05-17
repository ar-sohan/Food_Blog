<?php

    session_start();
    if(!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])){
        require_once('model/userModel.php');
        $u = findUserByRememberToken($_COOKIE['remember_token']);
        if($u){
            $_SESSION['user_id'] = $u['id'];
            $_SESSION['name']    = $u['name'];
            $_SESSION['role']    = $u['role'];
        }
    }

    header('location: view/home.php');

?>