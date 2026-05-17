<?php
    session_start();
    require_once('../model/menuItemModel.php');

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
    $item = $id > 0 ? getMenuItemById($id) : null;
    if(!$item){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Menu item not found.'];
        header('location: ../view/admin/restaurants.php');
        exit;
    }
    $restaurantId = (int)$item['restaurant_id'];

    if(deleteMenuItem($id)){
        $_SESSION['flash'] = ['type'=>'success', 'msg'=>'Menu item deleted.'];
    } else {
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Could not delete menu item.'];
    }

    header('location: ../view/admin/menuItems.php?restaurant_id=' . $restaurantId);
    exit;
?>