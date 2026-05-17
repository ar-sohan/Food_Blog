<?php
    session_start();
    require_once('../model/restaurantModel.php');

    function shortText($text, $limit = 140){
        $text = $text ?? '';
        if(function_exists('mb_strimwidth')){
            return mb_strimwidth($text, 0, $limit, '...');
        }
        return strlen($text) > $limit ? substr($text, 0, $limit) . '...' : $text;
    }

    $restaurants = getAllRestaurants();
    $pageTitle = "Restaurants - Online Food Blog";
    include('header.php');
?>

    <h1>Restaurants</h1>

    <?php if(empty($restaurants)){ ?>
        <p>No restaurants have been added yet. Check back soon!</p>
    <?php } else { ?>
        <div class="card-grid">
            <?php foreach($restaurants as $r){ ?>
                <a class="card" href="restaurantDetail.php?id=<?= (int)$r['id'] ?>">
                <h3><?= htmlspecialchars($r['name']) ?></h3>
                 <p class="muted"><?= htmlspecialchars($r['location']) ?> &middot; <?= htmlspecialchars($r['area']) ?></p>
                 <p><?= htmlspecialchars(shortText($r['short_background'] ?? '', 140)) ?></p>
                </a>
            <?php } ?>
        </div>
    <?php } ?>

<?php include('footer.php'); ?>
