<?php
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }

    if(!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin'){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Admin access required.'];
        header('location: ../login.php');
        exit;
    }

    if(!isset($_SESSION['csrf'])){
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    
    $cssPath         = '../../assets/css/style.css';
    $homePath        = '../home.php';
    $restaurantsPath = '../restaurants.php';
    $loginPath       = '../login.php';
    $signupPath      = '../signup.php';
    $adminPath       = 'dashboard.php';
    $profilePath     = '../profile.php';
    $logoutPath      = '../../controller/logout.php';
    $jsPath          = '../../assets/js/main.js';
?>