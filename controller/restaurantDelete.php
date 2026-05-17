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

    $id = (int)($_POST['id'] ?? 0);
    if($id <= 0 || !getRestaurantById($id)){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Restaurant not found.'];
        header('location: ../view/admin/restaurants.php');
        exit;
    }

    if(deleteRestaurant($id)){
        $_SESSION['flash'] = ['type'=>'success', 'msg'=>'Restaurant deleted (menu items cascaded).'];
    } else {
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Could not delete restaurant.'];
    }

    header('location: ../view/admin/restaurants.php');
    exit;
?>