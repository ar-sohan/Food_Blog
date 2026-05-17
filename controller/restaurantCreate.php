<?php
    session_start();
    require_once('../model/restaurantModel.php');

    // Admin gate.
    if(!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin'){
        header('location: ../view/login.php');
        exit;
    }
    if(!isset($_POST['submit'])){
        header('location: ../view/admin/restaurantCreate.php');
        exit;
    }
    if(!isset($_POST['csrf'], $_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Invalid request. Please reload and try again.'];
        header('location: ../view/admin/restaurantCreate.php');
        exit;
    }

    $name             = trim($_POST['name']             ?? '');
    $location         = trim($_POST['location']         ?? '');
    $area             = trim($_POST['area']             ?? '');
    $short_background = trim($_POST['short_background'] ?? '');
    $goals            = trim($_POST['goals']            ?? '');

    $errors = [];
    if($name === '' || strlen($name) > 150){ $errors[] = 'Name is required (max 150 chars).'; }
    if($location === '' || strlen($location) > 150){ $errors[] = 'Location is required (max 150 chars).'; }
    if($area === '' || strlen($area) > 150){ $errors[] = 'Area is required (max 150 chars).'; }

    if(!empty($errors)){
        $_SESSION['errors'] = $errors;
        $_SESSION['old']    = compact('name','location','area','short_background','goals');
        header('location: ../view/admin/restaurantCreate.php');
        exit;
    }

    $newId = createRestaurant([
        'name'             => $name,
        'location'         => $location,
        'area'             => $area,
        'short_background' => $short_background,
        'goals'            => $goals,
    ]);

    if($newId > 0){
        $_SESSION['flash'] = ['type'=>'success', 'msg'=>'Restaurant created.'];
        header('location: ../view/admin/restaurants.php');
    } else {
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Could not create restaurant. Please try again.'];
        $_SESSION['old']   = compact('name','location','area','short_background','goals');
        header('location: ../view/admin/restaurantCreate.php');
    }
    exit;
?>