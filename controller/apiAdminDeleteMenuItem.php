<?php
    header('Content-Type: application/json');
    session_start();
    require_once('../model/menuItemModel.php');

    if(!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin'){
        http_response_code(403);
        echo json_encode(['success'=>false, 'error'=>'Admin required.']);
        exit;
    }
    if(!isset($_POST['csrf'], $_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])){
        http_response_code(400);
        echo json_encode(['success'=>false, 'error'=>'Invalid CSRF token.']);
        exit;
    }

    $id = (int)($_POST['id'] ?? 0);
    if($id <= 0 || !getMenuItemById($id)){
        http_response_code(404);
        echo json_encode(['success'=>false, 'error'=>'Menu item not found.']);
        exit;
    }

    if(deleteMenuItem($id)){
        echo json_encode(['success'=>true]);
    } else {
        http_response_code(500);
        echo json_encode(['success'=>false, 'error'=>'Database delete failed.']);
    }
?>
