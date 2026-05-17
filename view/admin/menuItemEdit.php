<?php
    require_once('_gate.php');
    require_once('../../model/menuItemModel.php');
    require_once('../../model/restaurantModel.php');

    $id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $item = $id > 0 ? getMenuItemById($id) : null;
    if(!$item){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Menu item not found.'];
        header('location: restaurants.php');
        exit;
    }
    $restaurant = getRestaurantById($item['restaurant_id']);

    $errors = $_SESSION['errors'] ?? [];
    $old    = $_SESSION['old']    ?? [];
    unset($_SESSION['errors'], $_SESSION['old']);

    $pageTitle = "Edit Menu Item - Admin";
    $extraScripts = ['../../assets/js/admin.js'];
    include('../header.php');
?>

    <h1>Edit Menu Item</h1>
    <p>
        For <strong><?= htmlspecialchars($restaurant['name']) ?></strong> &middot;
        <a href="menuItems.php?restaurant_id=<?= (int)$item['restaurant_id'] ?>">&larr; Back to its menu</a>
    </p>

    <?php if(!empty($errors)){ ?>
        <div class="flash error">
            <?php foreach($errors as $e){ ?>
                <div><?= htmlspecialchars($e) ?></div>
            <?php } ?>
        </div>
    <?php } ?>

    <form method="post" action="../../controller/menuItemUpdate.php"
          id="menuItemForm" enctype="multipart/form-data" novalidate>
        <fieldset>
            <legend>Edit "<?= htmlspecialchars($item['name']) ?>"</legend>

            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <input type="hidden" name="id"   value="<?= (int)$item['id'] ?>">

            <label for="name">Name</label>
            <input type="text" name="name" id="name"
                   value="<?= htmlspecialchars($old['name'] ?? $item['name']) ?>" required>

            <label for="description">Description</label>
            <textarea name="description" id="description"><?= htmlspecialchars($old['description'] ?? ($item['description'] ?? '')) ?></textarea>

            <label for="price">Price (must be greater than 0)</label>
            <input type="number" name="price" id="price" step="0.01" min="0.01"
                   value="<?= htmlspecialchars($old['price'] ?? $item['price']) ?>" required>

            <?php if(!empty($item['image_path'])){ ?>
                <p>Current image:</p>
                <img class="thumb" src="../../assets/uploads/menu/<?= htmlspecialchars($item['image_path']) ?>"
                     alt="<?= htmlspecialchars($item['name']) ?>">
            <?php } ?>

            <label for="image">Replace Image (optional, JPEG or PNG, max 2&nbsp;MB)</label>
            <input type="file" name="image" id="image" accept="image/jpeg, image/png">

            <input type="submit" name="submit" value="Save Changes">
        </fieldset>
    </form>

<?php include('../footer.php'); ?>