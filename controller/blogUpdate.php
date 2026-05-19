<?php
    session_start();
    require_once('../model/blogModel.php');

    if(!isset($_SESSION['user_id'])){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Please login to edit blogs.'];
        header('location: ../view/login.php');
        exit;
    }

    if(!isset($_POST['submit'])){
        header('location: ../view/blog.php');
        exit;
    }

    if(!isset($_POST['csrf'], $_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Invalid request. Please reload and try again.'];
        header('location: ../view/blog.php');
        exit;
    }

    $id = (int)($_POST['id'] ?? 0);
    $blog = $id > 0 ? getBlogPostById($id) : null;

    if(!$blog){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Blog not found.'];
        header('location: ../view/blog.php');
        exit;
    }

    if((int)$blog['user_id'] !== (int)$_SESSION['user_id']){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'You can only edit your own blog.'];
        header('location: ../view/blogDetail.php?id=' . (int)$blog['id']);
        exit;
    }

    $title    = trim($_POST['title'] ?? '');
    $content  = trim($_POST['content'] ?? '');
    $postType = trim($_POST['post_type'] ?? 'food');
    $allowedTypes = ['restaurant', 'food', 'both'];

    $titleLength = function_exists('mb_strlen') ? mb_strlen($title) : strlen($title);
    $contentLength = function_exists('mb_strlen') ? mb_strlen($content) : strlen($content);

    $errors = [];
    if($title === '' || $titleLength > 200){
        $errors[] = 'Title is required (max 200 characters).';
    }
    if($content === ''){
        $errors[] = 'Content is required.';
    }
    if($contentLength > 5000){
        $errors[] = 'Content must be 5000 characters or fewer.';
    }
    if(!in_array($postType, $allowedTypes, true)){
        $errors[] = 'Please choose a valid blog type.';
    }

    $imageFilename = null;
    if(isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE){
        $f = $_FILES['image'];
        if($f['error'] !== UPLOAD_ERR_OK){
            $errors[] = 'Could not upload the image.';
        } elseif($f['size'] > 2 * 1024 * 1024){
            $errors[] = 'Image must be 2 MB or less.';
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $f['tmp_name']);
            finfo_close($finfo);
            $allowedImages = ['image/jpeg'=>'jpg', 'image/png'=>'png'];
            if(!isset($allowedImages[$mime])){
                $errors[] = 'Image must be a JPEG or PNG.';
            } else {
                $uploadDir = __DIR__ . '/../assets/uploads/blog/';
                if(!is_dir($uploadDir)){
                    mkdir($uploadDir, 0777, true);
                }
                $ext = $allowedImages[$mime];
                $filename = 'b' . (int)$_SESSION['user_id'] . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                if(move_uploaded_file($f['tmp_name'], $uploadDir . $filename)){
                    $imageFilename = $filename;
                } else {
                    $errors[] = 'Could not save image on server.';
                }
            }
        }
    }

    if(!empty($errors)){
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = [
            'title' => $title,
            'content' => $content,
            'post_type' => $postType,
        ];
        header('location: ../view/blogEdit.php?id=' . (int)$blog['id']);
        exit;
    }

    if(updateBlogPost($id, [
        'title' => $title,
        'content' => $content,
        'image_path' => $imageFilename,
        'post_type' => $postType,
    ])){
        $_SESSION['flash'] = ['type'=>'success', 'msg'=>'Blog updated.'];
        header('location: ../view/blogDetail.php?id=' . $id);
    } else {
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Could not update blog. Please try again.'];
        header('location: ../view/blogEdit.php?id=' . $id);
    }
    exit;
?>
