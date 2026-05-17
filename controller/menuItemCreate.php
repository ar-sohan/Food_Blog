<?php
    session_start();
    require_once('../model/menuItemModel.php');
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

    $restaurantId = (int)($_POST['restaurant_id'] ?? 0);
    $name         = trim($_POST['name']           ?? '');
    $description  = trim($_POST['description']    ?? '');
    $price        = $_POST['price']               ?? '';

    if($restaurantId <= 0 || !getRestaurantById($restaurantId)){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Restaurant not found.'];
        header('location: ../view/admin/restaurants.php');
        exit;
    }

    $errors = [];
    if($name === '' || strlen($name) > 150){ $errors[] = 'Name is required (max 150 chars).'; }
    if($price === '' || !is_numeric($price) || (float)$price <= 0){
        $errors[] = 'Price must be a number greater than 0.';
    }

    // Handle image upload (optional on create).
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
        header('location: ../view/admin/menuItemCreate.php?restaurant_id=' . $restaurantId);
        exit;
    }

    $newId = createMenuItem([
        'restaurant_id' => $restaurantId,
        'name'          => $name,
        'description'   => $description,
        'price'         => (float)$price,
        'image_path'    => $imageFilename,
    ]);

    if($newId > 0){
        $_SESSION['flash'] = ['type'=>'success', 'msg'=>'Menu item added.'];
        header('location: ../view/admin/menuItems.php?restaurant_id=' . $restaurantId);
    } else {
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Could not add menu item.'];
        $_SESSION['old']   = compact('name','description','price');
        header('location: ../view/admin/menuItemCreate.php?restaurant_id=' . $restaurantId);
    }
    exit;
?>