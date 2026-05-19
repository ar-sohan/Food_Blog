<?php
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }
    $loggedIn = isset($_SESSION['user_id']);
    $role     = $loggedIn ? $_SESSION['role'] : 'visitor';
    $name     = $loggedIn ? $_SESSION['name'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Online Food Blog' ?></title>
    <link rel="stylesheet" href="<?= isset($cssPath) ? $cssPath : '../assets/css/style.css' ?>">
</head>
<body>
    <header class="site-header">
        <div class="brand">
            <a href="<?= isset($homePath) ? $homePath : 'home.php' ?>">ART Food Blog</a>
        </div>
        <nav class="site-nav">
            <a href="<?= isset($homePath) ? $homePath : 'home.php' ?>">Home</a>
            <a href="<?= isset($restaurantsPath) ? $restaurantsPath : 'restaurants.php' ?>">Restaurants</a>

            <?php if(!$loggedIn){ ?>
                <a href="<?= isset($loginPath) ? $loginPath : 'login.php' ?>">Login</a>
                <a href="<?= isset($signupPath) ? $signupPath : 'signup.php' ?>">Signup</a>
            <?php } else { ?>
                <a href="<?= isset($blogPath) ? $blogPath : 'blog.php' ?>">Blog</a>
                <?php if($role === 'admin'){ ?>
                    <a href="<?= isset($adminPath) ? $adminPath : 'admin/dashboard.php' ?>">Admin</a>
                <?php } ?>
                <a href="<?= isset($profilePath) ? $profilePath : 'profile.php' ?>">Profile (<?= htmlspecialchars($name) ?>)</a>
                <a href="<?= isset($logoutPath) ? $logoutPath : '../controller/logout.php' ?>">Logout</a>
            <?php } ?>
        </nav>
    </header>
    <main class="site-main">
