<?php
    session_start();
    require_once('../model/userModel.php');

    // Only accept actual form submissions; anything else bounces back to the form.
    if(!isset($_POST['submit'])){
        header('location:../view/signup.php');
        exit;
    }

    // CSRF check
    if(!isset($_POST['csrf'], $_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Invalid request. Please reload and try again.'];
        header('location: ../view/signup.php');
        exit;
    }

    // Pull + normalise inputs.
    $name      = trim($_POST['name']      ?? '');
    $email     = trim($_POST['email']     ?? '');
    $password  = $_POST['password']       ?? '';
    $password2 = $_POST['password2']      ?? '';
    $role      = $_POST['role']           ?? 'member';

    // Server-side validation.
    $errors = [];
    if($name === '' || strlen($name) > 100){
        $errors[] = 'Name is required (max 100 characters).';
    }
    if($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors[] = 'A valid email is required.';
    }
    if(strlen($password) <= 4){
        $errors[] = 'Password must be at least 8 characters long.';
    }
    if($password !== $password2){
        $errors[] = 'Passwords do not match.';
    }
    if(!in_array($role, ['admin', 'member'], true)){
        $errors[] = 'Invalid role selected.';
    }
    if(empty($errors) && emailExists($email)){
        $errors[] = 'An account with this email already exists.';
    }

    if(!empty($errors)){
        $_SESSION['errors'] = $errors;
        $_SESSION['old']    = ['name'=>$name, 'email'=>$email, 'role'=>$role];
        header('location: ../view/signup.php');
        exit;
    }

    // All good - create the user.
    $newId = addUser([
        'name'     => $name,
        'email'    => $email,
        'password' => $password,
        'role'     => $role,
    ]);

    if($newId > 0){
        $_SESSION['flash'] = ['type'=>'success', 'msg'=>'Account created. Please log in.'];
        header('location: ../view/login.php');
        exit;
    }

    $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Could not create account. Please try again.'];
    $_SESSION['old']   = ['name'=>$name, 'email'=>$email, 'role'=>$role];
    header('location: ../view/signup.php');
    exit;
?>
