<?php
    session_start();
    require_once('../model/userModel.php');

    if(!isset($_SESSION['user_id'])){
        header('location: ../view/login.php');
        exit;
    }
    if(!isset($_POST['submit'])){
        header('location: ../view/profile.php');
        exit;
    }
    if(!isset($_POST['csrf'], $_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Invalid request. Please reload and try again.'];
        header('location: ../view/profile.php');
        exit;
    }

    $userId  = (int)$_SESSION['user_id'];
    $current = $_POST['current_password']  ?? '';
    $new     = $_POST['new_password']      ?? '';
    $new2    = $_POST['new_password2']     ?? '';

    $errors = [];
    if($current === ''){ $errors[] = 'Current password is required.'; }
    if(strlen($new) < 8){ $errors[] = 'New password must be at least 8 characters.'; }
    if($new !== $new2){ $errors[] = 'New passwords do not match.'; }

    if(empty($errors)){
        $user = findUserById($userId);
        if(!$user || !password_verify($current, $user['password_hash'])){
            $errors[] = 'Current password is incorrect.';
        }
    }

    if(!empty($errors)){
        $_SESSION['errors'] = $errors;
        header('location: ../view/profile.php');
        exit;
    }

    $newHash = password_hash($new, PASSWORD_DEFAULT);
    if(updateUserPassword($userId, $newHash)){
        $_SESSION['flash'] = ['type'=>'success', 'msg'=>'Password changed.'];
    } else {
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Could not change password. Please try again.'];
    }
    header('location: ../view/profile.php');
    exit;
?>
