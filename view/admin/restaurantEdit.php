<?php
    require_once('_gate.php');
    require_once('../../model/restaurantModel.php');

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $r  = $id > 0 ? getRestaurantById($id) : null;
    if(!$r){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Restaurant not found.'];
        header('location: restaurants.php');
        exit;
    }

    $errors = $_SESSION['errors'] ?? [];
    $old    = $_SESSION['old']    ?? [];
    unset($_SESSION['errors'], $_SESSION['old']);

    $pageTitle = "Edit Restaurant - Admin";
    $extraScripts = ['../../assets/js/admin.js'];
    include('../header.php');
?>

    <h1>Edit Restaurant</h1>
    <p><a href="restaurants.php">&larr; Back to list</a></p>

    <?php if(!empty($errors)){ ?>
        <div class="flash error">
            <?php foreach($errors as $e){ ?>
                <div><?= htmlspecialchars($e) ?></div>
            <?php } ?>
        </div>
    <?php } ?>

    <form method="post" action="../../controller/restaurantUpdate.php"
          id="restaurantForm" novalidate>
        <fieldset>
            <legend>Edit "<?= htmlspecialchars($r['name']) ?>"</legend>

            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <input type="hidden" name="id"   value="<?= (int)$r['id'] ?>">

            <label for="name">Name</label>
            <input type="text" name="name" id="name"
                   value="<?= htmlspecialchars($old['name'] ?? $r['name']) ?>" required>

            <label for="location">Location (city)</label>
            <input type="text" name="location" id="location"
                   value="<?= htmlspecialchars($old['location'] ?? $r['location']) ?>" required>

            <label for="area">Area (neighborhood)</label>
            <input type="text" name="area" id="area"
                   value="<?= htmlspecialchars($old['area'] ?? $r['area']) ?>" required>

            <label for="short_background">Short Background</label>
            <textarea name="short_background" id="short_background"><?= htmlspecialchars($old['short_background'] ?? ($r['short_background'] ?? '')) ?></textarea>

            <label for="goals">Goals</label>
            <textarea name="goals" id="goals"><?= htmlspecialchars($old['goals'] ?? ($r['goals'] ?? '')) ?></textarea>

            <input type="submit" name="submit" value="Save Changes">
        </fieldset>
    </form>

<?php include('../footer.php'); ?>