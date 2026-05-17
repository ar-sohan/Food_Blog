<?php
    

    header('Content-Type: application/json');
    session_start();
    require_once('../model/reviewModel.php');

    if($_SERVER['REQUEST_METHOD'] !== 'DELETE'){
        http_response_code(405);
        echo json_encode(['success'=>false, 'error'=>'DELETE required.']);
        exit;
    }
    if(!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'member'){
        http_response_code(403);
        echo json_encode(['success'=>false, 'error'=>'Members only.']);
        exit;
    }

    $csrf = $_GET['csrf'] ?? '';
    if(!isset($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $csrf)){
        http_response_code(400);
        echo json_encode(['success'=>false, 'error'=>'Invalid CSRF token.']);
        exit;
    }

    $id = (int)($_GET['id'] ?? 0);
    if($id <= 0){
        http_response_code(400);
        echo json_encode(['success'=>false, 'error'=>'Review id is required.']);
        exit;
    }

    $review = findReviewById($id);
    if(!$review){
        http_response_code(404);
        echo json_encode(['success'=>false, 'error'=>'Review not found.']);
        exit;
    }
    if((int)$review['user_id'] !== (int)$_SESSION['user_id']){
        http_response_code(403);
        echo json_encode(['success'=>false, 'error'=>'You can only delete your own reviews.']);
        exit;
    }

    if(deleteReviewByIdAndUser($id, (int)$_SESSION['user_id'])){
        echo json_encode(['success'=>true]);
    } else {
        http_response_code(500);
        echo json_encode(['success'=>false, 'error'=>'Could not delete review.']);
    }
?>
