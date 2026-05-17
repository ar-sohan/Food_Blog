<?php
    require_once('_gate.php');
    require_once('../../model/restaurantModel.php');
    require_once('../../model/menuItemModel.php');

    $restaurantId = isset($_GET['restaurant_id']) ? (int)$_GET['restaurant_id'] : 0;
    $restaurant   = $restaurantId > 0 ? getRestaurantById($restaurantId) : null;
    if(!$restaurant){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Restaurant not found.'];
        header('location: restaurants.php');
        exit;
    }

    $items = getMenuItemsByRestaurant($restaurantId);
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    $pageTitle = "Menu Items - " . htmlspecialchars($restaurant['name']);
    $extraScripts = ['../../assets/js/admin.js'];
    include('../header.php');
?>

    <h1>Menu Items &mdash; <?= htmlspecialchars($restaurant['name']) ?></h1>
    <p>
        <a href="restaurants.php">&larr; All restaurants</a> |
        <a href="restaurantEdit.php?id=<?= (int)$restaurant['id'] ?>">Edit restaurant</a>
    </p>

    <?php if($flash){ ?>
        <div class="flash <?= $flash['type'] === 'error' ? 'error' : '' ?>">
            <?= htmlspecialchars($flash['msg']) ?>
        </div>
    <?php } ?>

    <p><a class="btn" href="menuItemCreate.php?restaurant_id=<?= (int)$restaurant['id'] ?>">+ Add Menu Item</a></p>

    <?php if(empty($items)){ ?>
        <p>No menu items for this restaurant yet.</p>
    <?php } else { ?>
        <table class="data">
            <tr>
                <th>Image</th><th>Name</th><th>Price</th><th>Description</th><th>Actions</th>
            </tr>
            <?php foreach($items as $m){ ?>
                <tr id="menuItem-row-<?= (int)$m['id'] ?>">
                    <td>
                        <?php if(!empty($m['image_path'])){ ?>
                            <img class="thumb" src="../../assets/uploads/menu/<?= htmlspecialchars($m['image_path']) ?>"
                                 alt="<?= htmlspecialchars($m['name']) ?>">
                        <?php } else { ?>
                            <span class="muted">-</span>
                        <?php } ?>
                    </td>
                    <td><?= htmlspecialchars($m['name']) ?></td>
                    <td>&#36;<?= number_format((float)$m['price'], 2) ?></td>
                    <td><?= htmlspecialchars(mb_strimwidth($m['description'] ?? '', 0, 80, '...')) ?></td>
                    <td>
                        <a class="btn btn-small btn-secondary" href="menuItemEdit.php?id=<?= (int)$m['id'] ?>">Edit</a>
                        <button class="btn btn-small btn-danger js-delete-menu-item"
                                data-id="<?= (int)$m['id'] ?>"
                                data-csrf="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                            Delete
                        </button>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>

<?php include('../footer.php'); ?>