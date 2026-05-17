<?php
    session_start();
    require_once('../model/restaurantModel.php');
    require_once('../model/menuItemModel.php');

    function shortText($text, $limit = 120){
        $text = $text ?? '';
        if(function_exists('mb_strimwidth')){
            return mb_strimwidth($text, 0, $limit, '...');
        }
        return strlen($text) > $limit ? substr($text, 0, $limit) . '...' : $text;
    }

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $restaurant = $id > 0 ? getRestaurantById($id) : null;

    if(!$restaurant){
        $pageTitle = "Restaurant not found";
        include('header.php');
        echo '<h1>Restaurant not found</h1>';
        echo '<p><a href="restaurants.php">Back to all restaurants</a></p>';
        include('footer.php');
        exit;
    }

    $menu = getMenuItemsByRestaurant($restaurant['id']);
    $pageTitle = htmlspecialchars($restaurant['name']) . " - Online Food Blog";
    include('header.php');
?>

    <h1><?= htmlspecialchars($restaurant['name']) ?></h1>
    <p class="muted">
        <?= htmlspecialchars($restaurant['location']) ?> &middot; <?= htmlspecialchars($restaurant['area']) ?>
    </p>

    <?php if(!empty($restaurant['short_background'])){ ?>
        <h3>About</h3>
        <p><?= nl2br(htmlspecialchars($restaurant['short_background'])) ?></p>
    <?php } ?>

    <?php if(!empty($restaurant['goals'])){ ?>
        <h3>Goals</h3>
        <p><?= nl2br(htmlspecialchars($restaurant['goals'])) ?></p>
    <?php } ?>

    <h2>Menu</h2>
    <?php if(empty($menu)){ ?>
        <p>No menu items have been added yet.</p>
    <?php } else { ?>
        <div class="card-grid">
        <?php foreach($menu as $m){ ?>
            <a class="card" href="menuItem.php?id=<?= (int)$m['id'] ?>">
            <?php if(!empty($m['image_path'])){ ?>
            <img src="../assets/uploads/menu/<?= htmlspecialchars($m['image_path']) ?>"
          alt="<?= htmlspecialchars($m['name']) ?>">
        <?php } ?>
            <h3><?= htmlspecialchars($m['name']) ?></h3>
            <p class="price">&#36;<?= number_format((float)$m['price'], 2) ?></p>
            <p><?= htmlspecialchars(shortText($m['description'] ?? '', 120)) ?></p>
                </a>
            <?php } ?>
        </div>
    <?php } ?>

    <p><a href="restaurants.php">&larr; All restaurants</a></p>

<?php include('footer.php'); ?>
