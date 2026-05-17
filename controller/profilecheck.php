<?php
    session_start();
    require_once('../model/userModel.php');

    // Session gate.
    if(!isset($_SESSION['user_id'])){
        header('location: ../view/login.php');
        exit;
    }
    if(!isset($_POST['submit'])){
        header('location: ../view/profile.php');
        exit;
    }

    // CSRF.
    if(!isset($_POST['csrf'], $_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Invalid request. Please reload and try again.'];
        header('location: ../view/profile.php');
        exit;
    }

    $userId = (int)$_SESSION['user_id'];
    $name   = trim($_POST['name']  ?? '');
    $email  = trim($_POST['email'] ?? '');

    $errors = [];
    if($name === '' || strlen($name) > 100){
        $errors[] = 'Name is required (max 100 characters).';
    }
    if($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors[] = 'A valid email is required.';
    }
    if(empty($errors) && emailExistsForOtherUser($email, $userId)){
        $errors[] = 'That email is already used by another account.';
    }

    // Handle optional profile picture upload.
    $newPicFilename = null;
    if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE){
        $f = $_FILES['profile_picture'];
        if($f['error'] !== UPLOAD_ERR_OK){
            $errors[] = 'Could not upload the picture. Please try again.';
        } elseif($f['size'] > 2 * 1024 * 1024){
            $errors[] = 'Picture must be 2 MB or less.';
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $f['tmp_name']);
            finfo_close($finfo);
            $allowed = ['image/jpeg'=>'jpg', 'image/png'=>'png'];
            if(!isset($allowed[$mime])){
                $errors[] = 'Picture must be a JPEG or PNG image.';
            } else {
                $ext      = $allowed[$mime];
                $filename = 'u' . $userId . '_' . time() . '.' . $ext;
                $dest     = __DIR__ . '/../assets/uploads/profile/' . $filename;
                if(move_uploaded_file($f['tmp_name'], $dest)){
                    $newPicFilename = $filename;
                } else {
                    $errors[] = 'Could not save the picture on the server.';
                }
            }
        }
    }

    if(!empty($errors)){
        $_SESSION['errors'] = $errors;
        $_SESSION['old']    = ['name'=>$name, 'email'=>$email];
        header('location: ../view/profile.php');
        exit;
    }

    if(updateUserProfile($userId, $name, $email, $newPicFilename)){
        // Refresh the name in the session so the navbar updates immediately.
        $_SESSION['name'] = $name;
        $_SESSION['flash'] = ['type'=>'success', 'msg'=>'Profile updated.'];
    } else {
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Could not update profile. Please try again.'];
    }

    header('location: ../view/profile.php');
    exit;
?>
