<?php
    require_once('_gate.php');
    require_once('../../model/restaurantModel.php');
    require_once('../../model/menuItemModel.php');
    require_once('../../model/db.php');

    $restaurantCount = countRestaurants();
    $menuItemCount   = countMenuItems();

    $con = getConnection();
    $r = mysqli_fetch_assoc(mysqli_query($con, "select count(*) as c from reviews"));
    $reviewCount = (int)$r['c'];
    $r = mysqli_fetch_assoc(mysqli_query($con, "select count(*) as c from food_experience_posts"));
    $foodExpCount = (int)$r['c'];

    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    $pageTitle = "Admin Dashboard - Online Food Blog";
    include('../header.php');
?>

    <h1>Admin Dashboard</h1>

    <?php if($flash){ ?>
        <div class="flash <?= $flash['type'] === 'error' ? 'error' : '' ?>">
            <?= htmlspecialchars($flash['msg']) ?>
        </div>
    <?php } ?>

    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-number"><?= $restaurantCount ?></div>
            <div class="stat-label">Restaurants</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $menuItemCount ?></div>
            <div class="stat-label">Menu Items</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $reviewCount ?></div>
            <div class="stat-label">Reviews</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $foodExpCount ?></div>
            <div class="stat-label">Blogs</div>
        </div>
    </div>

    <h2>Manage</h2>
    <p>
        <a class="btn" href="restaurants.php">Restaurants &amp; Menus</a>
        <a class="btn btn-secondary" href="restaurantCreate.php">+ Add Restaurant</a>
        <a class="btn btn-secondary" href="../blogCreate.php">+ Write Blog</a>
    </p>

<?php include('../footer.php'); ?>
