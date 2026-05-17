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

    $id          = (int)($_POST['id']         ?? 0);
    $name        = trim($_POST['name']        ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = $_POST['price']            ?? '';

    $existing = $id > 0 ? getMenuItemById($id) : null;
    if(!$existing){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Menu item not found.'];
        header('location: ../view/admin/restaurants.php');
        exit;
    }
    $restaurantId = (int)$existing['restaurant_id'];

    $errors = [];
    if($name === '' || strlen($name) > 150){ $errors[] = 'Name is required (max 150 chars).'; }
    if($price === '' || !is_numeric($price) || (float)$price <= 0){
        $errors[] = 'Price must be a number greater than 0.';
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
            $allowed = ['image/jpeg'=>'jpg', 'image/png'=>'png'];
            if(!isset($allowed[$mime])){
                $errors[] = 'Image must be a JPEG or PNG.';
            } else {
                $ext      = $allowed[$mime];
                $filename = 'r' . $restaurantId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $dest     = __DIR__ . '/../assets/uploads/menu/' . $filename;
                if(move_uploaded_file($f['tmp_name'], $dest)){
                    $imageFilename = $filename;
                } else {
                    $errors[] = 'Could not save image on server.';
                }
            }
        }
    }

    if(!empty($errors)){
        $_SESSION['errors'] = $errors;
        $_SESSION['old']    = compact('name','description','price');
        header('location: ../view/admin/menuItemEdit.php?id=' . $id);
        exit;
    }

    if(updateMenuItem($id, [
        'name'        => $name,
        'description' => $description,
        'price'       => (float)$price,
        'image_path'  => $imageFilename,   // null = keep existing
    ])){
        $_SESSION['flash'] = ['type'=>'success', 'msg'=>'Menu item updated.'];
    } else {
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Could not update menu item.'];
    }

    header('location: ../view/admin/menuItems.php?restaurant_id=' . $restaurantId);
    exit;
?>