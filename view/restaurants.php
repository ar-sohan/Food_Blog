<?php
    session_start();
    require_once('../model/restaurantModel.php');

    $restaurants = getAllRestaurants();
    $pageTitle = "Restaurants - Online Food Blog";
    $extraScripts = ['../assets/js/search.js'];
    include('header.php');
?>

    <h1>Restaurants</h1>

    <?php include('searchBox.php'); ?>

    <div id="defaultList">
        <?php if(empty($restaurants)){ ?>
            <p>No restaurants have been added yet. Check back soon!</p>
        <?php } else { ?>
            <div class="card-grid">
                <?php foreach($restaurants as $r){ ?>
                    <a class="card" href="restaurantDetail.php?id=<?= (int)$r['id'] ?>">
                        <h3><?= htmlspecialchars($r['name']) ?></h3>
                        <p class="muted"><?= htmlspecialchars($r['location']) ?> &middot; <?= htmlspecialchars($r['area']) ?></p>
                        <p><?= htmlspecialchars(mb_strimwidth($r['short_background'] ?? '', 0, 140, '...')) ?></p>
                    </a>
                <?php } ?>
            </div>
        <?php } ?>
    </div>

<?php include('footer.php'); ?>
