<?php
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }

    require_once(__DIR__ . '/../model/userModel.php');

    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        header('location: ../view/login.php');
        exit;
    }

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $errors = [];
    if($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors[] = 'A valid email is required.';
    }
    if($password === ''){
        $errors[] = 'Password is required.';
    }

    if(!empty($errors)){
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = ['email' => $email];
        header('location: ../view/login.php');
        exit;
    }

    $user = loginUser($email, $password);

    if($user){
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        header('location: ../view/home.php');
        exit;
    }

    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Invalid email or password.'];
    $_SESSION['old'] = ['email' => $email];
    header('location: ../view/login.php');
    exit;
