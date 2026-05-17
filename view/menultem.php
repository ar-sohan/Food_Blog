<?php
    session_start();
    require_once('../model/menuItemModel.php');
    require_once('../model/restaurantModel.php');
    require_once('../model/reviewModel.php');

    $id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $item = $id > 0 ? getMenuItemById($id) : null;

    if(!$item){
        $pageTitle = "Menu item not found";
        include('header.php');
        echo '<h1>Menu item not found</h1>';
        echo '<p><a href="restaurants.php">Back to all restaurants</a></p>';
        include('footer.php');
        exit;
    }

    if(!isset($_SESSION['csrf'])){
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    $restaurant = getRestaurantById($item['restaurant_id']);
    $reviews    = getReviewsForMenuItem($item['id']);
    $loggedIn   = isset($_SESSION['user_id']);
    $isMember   = $loggedIn && ($_SESSION['role'] ?? '') === 'member';
    $currentUid = $loggedIn ? (int)$_SESSION['user_id'] : 0;

    $pageTitle = htmlspecialchars($item['name']) . " - Online Food Blog";
    $extraScripts = ['../assets/js/reviews.js'];
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

    <section id="reviews" class="review-section"
             data-menu-item-id="<?= (int)$item['id'] ?>"
             data-csrf="<?= htmlspecialchars($_SESSION['csrf']) ?>">
        <h3>Reviews <span class="muted">(<span id="reviewCount"><?= count($reviews) ?></span>)</span></h3>

        <ul id="reviewList" class="review-list">
            <?php foreach($reviews as $rev){ ?>
                <li class="review" id="review-<?= (int)$rev['id'] ?>">
                    <div class="review-head">
                        <strong><?= htmlspecialchars($rev['user_name']) ?></strong>
                        <span class="muted">&middot; <?= htmlspecialchars($rev['created_at']) ?></span>
                    </div>
                    <div class="review-body"><?= nl2br(htmlspecialchars($rev['comment'])) ?></div>
                    <?php if($isMember && (int)$rev['user_id'] === $currentUid){ ?>
                        <button class="btn btn-small btn-danger js-delete-review"
                                data-id="<?= (int)$rev['id'] ?>">Delete</button>
                    <?php } ?>
                </li>
            <?php } ?>
        </ul>
        <p id="noReviewsMsg" class="muted" <?= count($reviews) === 0 ? '' : 'style="display:none"' ?>>
            No reviews yet. Be the first to share your experience!
        </p>

        <?php if($isMember){ ?>
            <form id="reviewForm" novalidate>
                <fieldset>
                    <legend>Post a Review</legend>
                    <label for="reviewName">Posting as</label>
                    <input type="text" id="reviewName" value="<?= htmlspecialchars($_SESSION['name']) ?>" readonly>

                    <label for="reviewComment">Your review (max 1000 characters)</label>
                    <textarea id="reviewComment" maxlength="1000" required></textarea>

                    <div id="reviewError" class="error"></div>
                    <input type="submit" value="Post Review">
                </fieldset>
            </form>
        <?php } elseif($loggedIn){ ?>
            <p class="muted">Admin accounts can moderate reviews but not post them.</p>
        <?php } else { ?>
            <p class="muted">
                <a href="login.php">Log in</a> as a member to post a review.
            </p>
        <?php } ?>
    </section>

    <p>
        <?php if($restaurant){ ?>
            <a href="restaurantDetail.php?id=<?= (int)$restaurant['id'] ?>">&larr; Back to <?= htmlspecialchars($restaurant['name']) ?></a>
        <?php } else { ?>
            <a href="restaurants.php">&larr; All restaurants</a>
        <?php } ?>
    </p>

<?php include('footer.php'); ?>
