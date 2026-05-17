<?php
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }
    require_once(__DIR__ . '/../model/userModel.php');

    if(!isset($_POST['submit'])){
        header('location: ../view/login.php');
        exit;
    }

    // CSRF check
    if(!isset($_POST['csrf'], $_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Invalid request. Please reload and try again.'];
        header('location: ../view/login.php');
        exit;
    }

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password']    ?? '';
    $remember = isset($_POST['remember']);

    $errors = [];
    if($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors[] = 'A valid email is required.';
    }
    if($password === ''){
        $errors[] = 'Password is required.';
    }

    if(!empty($errors)){
        $_SESSION['errors'] = $errors;
        $_SESSION['old']    = ['email'=>$email];
        header('location: ../view/login.php');
        exit;
    }

    $user = findUserByEmail($email);
    if(!$user || !password_verify($password, $user['password_hash'])){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Invalid email or password.'];
        $_SESSION['old']   = ['email'=>$email];
        header('location: ../view/login.php');
        exit;
    }

    //session set - login successful
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['name']    = $user['name'];
    $_SESSION['role']    = $user['role'];

    //remember Me-create a  token(rawtoken), store sha256 hash,  30-day cookie.
    if($remember){
        $rawToken = bin2hex(random_bytes(32));
        setUserRememberToken($user['id'], $rawToken);
        setcookie(
            'remember_token',
            $rawToken,
            time() + 30 * 24 * 60 * 60,
            '/',
            '',
            false, // set to true if serve over HTTPS
            true   // HttpOnly
        );
    }

    header('location: ../view/home.php');
    exit;
