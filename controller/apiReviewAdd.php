<?php

    session_start();
    require_once('../model/reviewModel.php');
    require_once('../model/menuItemModel.php');

    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    function reviewResponse($status, $payload, $menuItemId = 0){
        global $isAjax;

        if($isAjax){
            http_response_code($status);
            header('Content-Type: application/json');
            echo json_encode($payload);
            exit;
        }

        $_SESSION['flash'] = [
            'type' => !empty($payload['success']) ? 'success' : 'error',
            'msg'  => !empty($payload['success']) ? 'Review posted successfully.' : ($payload['error'] ?? 'Could not post review.')
        ];
        $redirectId = $menuItemId > 0 ? $menuItemId : (int)($_POST['menu_item_id'] ?? 0);
        header('location: ../view/menuItem.php' . ($redirectId > 0 ? '?id=' . $redirectId : ''));
        exit;
    }

    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        reviewResponse(405, ['success'=>false, 'error'=>'POST required.']);
    }
    if(!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'member'){
        reviewResponse(403, ['success'=>false, 'error'=>'Members only.']);
    }
    if(!isset($_POST['csrf'], $_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])){
        reviewResponse(400, ['success'=>false, 'error'=>'Invalid CSRF token.']);
    }

    $menuItemId = (int)($_POST['menu_item_id'] ?? 0);
    $comment    = trim($_POST['comment'] ?? '');

    if($menuItemId <= 0 || !getMenuItemById($menuItemId)){
        reviewResponse(404, ['success'=>false, 'error'=>'Menu item not found.'], $menuItemId);
    }
    if($comment === ''){
        reviewResponse(400, ['success'=>false, 'error'=>'Comment is required.'], $menuItemId);
    }
    $commentLength = function_exists('mb_strlen') ? mb_strlen($comment) : strlen($comment);
    if($commentLength > 1000){
        reviewResponse(400, ['success'=>false, 'error'=>'Comment must be 1000 characters or fewer.'], $menuItemId);
    }

    $review = addReview($menuItemId, (int)$_SESSION['user_id'], $comment);
    if(!$review){
        reviewResponse(500, ['success'=>false, 'error'=>'Could not save review.'], $menuItemId);
    }

    reviewResponse(200, [
        'success' => true,
        'review'  => $review,
    ], $menuItemId);
?>
