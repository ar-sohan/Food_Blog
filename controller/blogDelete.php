<?php
    session_start();
    require_once('../model/blogModel.php');

    if(!isset($_SESSION['user_id'])){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Please login to delete blogs.'];
        header('location: ../view/login.php');
        exit;
    }

    if($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET'){
        header('location: ../view/blog.php');
        exit;
    }

    $csrf = $_POST['csrf'] ?? $_GET['csrf'] ?? '';
    if(!isset($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $csrf)){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Invalid request. Please reload and try again.'];
        header('location: ../view/blog.php');
        exit;
    }

    $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
    $blog = $id > 0 ? getBlogPostById($id) : null;

    if(!$blog){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Blog not found.'];
        header('location: ../view/blog.php');
        exit;
    }

    $isOwner = (int)$blog['user_id'] === (int)$_SESSION['user_id'];
    $isAdmin = ($_SESSION['role'] ?? '') === 'admin';

    if(!$isOwner && !$isAdmin){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'You are not allowed to delete this blog.'];
        header('location: ../view/blogDetail.php?id=' . (int)$blog['id']);
        exit;
    }

    if(deleteBlogPost($id)){
        $_SESSION['flash'] = ['type'=>'success', 'msg'=>'Blog deleted.'];
        header('location: ../view/blog.php');
    } else {
        $dbError = function_exists('getLastBlogDbError') ? getLastBlogDbError() : '';
        $_SESSION['flash'] = [
            'type'=>'error',
            'msg'=>'Could not delete blog.' . ($dbError !== '' ? ' DB error: ' . $dbError : '')
        ];
        header('location: ../view/blogDetail.php?id=' . $id);
    }
    exit;
?>
