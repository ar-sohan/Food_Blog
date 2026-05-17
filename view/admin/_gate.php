<?php
    // Include this at the top of every page inside view/admin/.
    // It (1) session_start()s, (2) rejects non-admins, and (3) sets the
    // header/footer path overrides so shared header.php/footer.php resolve
    // correctly from this deeper folder.

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

    // Path overrides for header.php / footer.php from view/admin/.
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