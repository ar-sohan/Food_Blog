<?php
    session_start();
    require_once('../model/menuItemModel.php');
    require_once('../model/restaurantModel.php');

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $item = $id > 0 ? getMenuItemById($id) : null;

    if(!$item){
        $pageTitle = "Menu item not found";
        include('header.php');
        echo '<h1>Menu item not found</h1>';
        echo '<p><a href="restaurants.php">Back to all restaurants</a></p>';
        include('footer.php');
        exit;
    }

    $restaurant = getRestaurantById($item['restaurant_id']);
    $pageTitle = htmlspecialchars($item['name']) . " - Online Food Blog";
    include('header.php');
?>

    <h1><?= htmlspecialchars($item['name']) ?></h1>
    <?php if($restaurant){ ?>
        <p class="muted">
            From <a href="restaurantDetail.php?id=<?= (int)$restaurant['id'] ?>"><?= htmlspecialchars($restaurant['name']) ?></a>
            &middot; <?= htmlspecialchars($restaurant['location']) ?>
        </p>
    <?php } ?>

    <?php if(!empty($item['image_path'])){ ?>
        <img class="item-hero"
             src="../assets/uploads/menu/<?= htmlspecialchars($item['image_path']) ?>"
             alt="<?= htmlspecialchars($item['name']) ?>">
    <?php } ?>

    <p class="price">&#36;<?= number_format((float)$item['price'], 2) ?></p>

    <?php if(!empty($item['description'])){ ?>
        <h3>Description</h3>
        <p><?= nl2br(htmlspecialchars($item['description'])) ?></p>
    <?php } ?>

    <section id="reviews" class="review-section">
        <h3>Reviews</h3>
        <!-- Task 3 will render the review list and the (member-only) post form here. -->
        <p class="muted">Review system coming soon.</p>
    </section>

    <p>
        <?php if($restaurant){ ?>
            <a href="restaurantDetail.php?id=<?= (int)$restaurant['id'] ?>">&larr; Back to <?= htmlspecialchars($restaurant['name']) ?></a>
        <?php } else { ?>
            <a href="restaurants.php">&larr; All restaurants</a>
        <?php } ?>
    </p>

<?php include('footer.php'); ?>
