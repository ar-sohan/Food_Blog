<?php
    session_start();
    require_once('../model/restaurantModel.php');

    if(!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin'){
        header('location: ../view/login.php');
        exit;
    }
    if(!isset($_POST['submit'])){
        header('location: ../view/admin/restaurants.php');
        exit;
    }
    if(!isset($_POST['csrf'], $_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Invalid request. Please reload and try again.'];
        header('location: ../view/admin/restaurants.php');
        exit;
    }

    $id               = (int)($_POST['id']             ?? 0);
    $name             = trim($_POST['name']            ?? '');
    $location         = trim($_POST['location']        ?? '');
    $area             = trim($_POST['area']            ?? '');
    $short_background = trim($_POST['short_background'] ?? '');
    $goals            = trim($_POST['goals']           ?? '');

    if($id <= 0 || !getRestaurantById($id)){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Restaurant not found.'];
        header('location: ../view/admin/restaurants.php');
        exit;
    }

    $errors = [];
    if($name === '' || strlen($name) > 150){ $errors[] = 'Name is required (max 150 chars).'; }
    if($location === '' || strlen($location) > 150){ $errors[] = 'Location is required (max 150 chars).'; }
    if($area === '' || strlen($area) > 150){ $errors[] = 'Area is required (max 150 chars).'; }

    if(!empty($errors)){
        $_SESSION['errors'] = $errors;
        $_SESSION['old']    = compact('name','location','area','short_background','goals');
        header('location: ../view/admin/restaurantEdit.php?id=' . $id);
        exit;
    }

    if(updateRestaurant($id, [
        'name'             => $name,
        'location'         => $location,
        'area'             => $area,
        'short_background' => $short_background,
        'goals'            => $goals,
    ])){
        $_SESSION['flash'] = ['type'=>'success', 'msg'=>'Restaurant updated.'];
    } else {
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Could not update restaurant. Please try again.'];
    }

    header('location: ../view/admin/restaurants.php');
    exit;
?>