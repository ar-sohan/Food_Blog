<?php
    require_once('_gate.php');
    require_once('../../model/restaurantModel.php');

    $restaurantId = isset($_GET['restaurant_id']) ? (int)$_GET['restaurant_id'] : 0;
    $restaurant   = $restaurantId > 0 ? getRestaurantById($restaurantId) : null;
    if(!$restaurant){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Restaurant not found.'];
        header('location: restaurants.php');
        exit;
    }

    $errors = $_SESSION['errors'] ?? [];
    $old    = $_SESSION['old']    ?? [];
    unset($_SESSION['errors'], $_SESSION['old']);

    $pageTitle = "Add Menu Item - Admin";
    $extraScripts = ['../../assets/js/admin.js'];
    include('../header.php');
?>

    <h1>Add Menu Item</h1>
    <p>
        For <strong><?= htmlspecialchars($restaurant['name']) ?></strong> &middot;
        <a href="menuItems.php?restaurant_id=<?= (int)$restaurant['id'] ?>">&larr; Back to its menu</a>
    </p>

    <?php if(!empty($errors)){ ?>
        <div class="flash error">
            <?php foreach($errors as $e){ ?>
                <div><?= htmlspecialchars($e) ?></div>
            <?php } ?>
        </div>
    <?php } ?>

    <form method="post" action="../../controller/menuItemCreate.php"
          id="menuItemForm" enctype="multipart/form-data" novalidate>
        <fieldset>
            <legend>New Menu Item</legend>

            <input type="hidden" name="csrf"          value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <input type="hidden" name="restaurant_id" value="<?= (int)$restaurant['id'] ?>">

            <label for="name">Name</label>
            <input type="text" name="name" id="name"
                   value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>

            <label for="description">Description</label>
            <textarea name="description" id="description"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>

            <label for="price">Price (must be greater than 0)</label>
            <input type="number" name="price" id="price" step="0.01" min="0.01"
                   value="<?= htmlspecialchars($old['price'] ?? '') ?>" required>

            <label for="image">Image (JPEG or PNG, max 2&nbsp;MB)</label>
            <input type="file" name="image" id="image" accept="image/jpeg, image/png">

            <input type="submit" name="submit" value="Create">
        </fieldset>
    </form>

<?php include('../footer.php'); ?>