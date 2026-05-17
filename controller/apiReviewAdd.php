<?php
    

    header('Content-Type: application/json');
    session_start();
    require_once('../model/reviewModel.php');
    require_once('../model/menuItemModel.php');

    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        http_response_code(405);
        echo json_encode(['success'=>false, 'error'=>'POST required.']);
        exit;
    }
    if(!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'member'){
        http_response_code(403);
        echo json_encode(['success'=>false, 'error'=>'Members only.']);
        exit;
    }
    if(!isset($_POST['csrf'], $_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])){
        http_response_code(400);
        echo json_encode(['success'=>false, 'error'=>'Invalid CSRF token.']);
        exit;
    }

    $menuItemId = (int)($_POST['menu_item_id'] ?? 0);
    $comment    = trim($_POST['comment'] ?? '');

    if($menuItemId <= 0 || !getMenuItemById($menuItemId)){
        http_response_code(404);
        echo json_encode(['success'=>false, 'error'=>'Menu item not found.']);
        exit;
    }
    if($comment === ''){
        http_response_code(400);
        echo json_encode(['success'=>false, 'error'=>'Comment is required.']);
        exit;
    }
    if(mb_strlen($comment) > 1000){
        http_response_code(400);
        echo json_encode(['success'=>false, 'error'=>'Comment must be 1000 characters or fewer.']);
        exit;
    }

    $review = addReview($menuItemId, (int)$_SESSION['user_id'], $comment);
    if(!$review){
        http_response_code(500);
        echo json_encode(['success'=>false, 'error'=>'Could not save review.']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'review'  => $review,
    ]);
?>
