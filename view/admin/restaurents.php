<?php
    require_once('_gate.php');
    require_once('../../model/restaurantModel.php');

    $restaurants = getAllRestaurants();
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    $pageTitle = "Manage Restaurants - Admin";
    $extraScripts = ['../../assets/js/admin.js'];
    include('../header.php');
?>

    <h1>Restaurants</h1>
    <p><a href="dashboard.php">&larr; Dashboard</a></p>

    <?php if($flash){ ?>
        <div class="flash <?= $flash['type'] === 'error' ? 'error' : '' ?>">
            <?= htmlspecialchars($flash['msg']) ?>
        </div>
    <?php } ?>

    <p><a class="btn" href="restaurantCreate.php">+ Add Restaurant</a></p>

    <?php if(empty($restaurants)){ ?>
        <p>No restaurants yet. Add the first one above.</p>
    <?php } else { ?>
        <table class="data">
            <tr>
                <th>Name</th><th>Location</th><th>Area</th><th>Actions</th>
            </tr>
            <?php foreach($restaurants as $r){ ?>
                <tr>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td><?= htmlspecialchars($r['location']) ?></td>
                    <td><?= htmlspecialchars($r['area']) ?></td>
                    <td>
                        <a class="btn btn-small" href="menuItems.php?restaurant_id=<?= (int)$r['id'] ?>">Menu</a>
                        <a class="btn btn-small btn-secondary" href="restaurantEdit.php?id=<?= (int)$r['id'] ?>">Edit</a>
                        <form method="post" action="../../controller/restaurantDelete.php"
                              class="inline-form"
                              onsubmit="return confirm('Delete this restaurant and all its menu items?');">
                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                            <input class="btn btn-small btn-danger" type="submit" name="submit" value="Delete">
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>

<?php include('../footer.php'); ?>