<?php
    session_start();
    require_once('../model/userModel.php');

    // Clear the Remember-Me token in the DB so the cookie can't be used again.
    if(isset($_SESSION['user_id'])){
        clearUserRememberToken($_SESSION['user_id']);
    }

    // Wipe the cookie.
    if(isset($_COOKIE['remember_token'])){
        setcookie('remember_token', '', time() - 3600, '/');
    }

    // Wipe the session.
    $_SESSION = [];
    if(ini_get("session.use_cookies")){
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();

    header('location: ../view/login.php');
    exit;
?>
