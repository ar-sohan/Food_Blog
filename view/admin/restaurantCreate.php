<?php
    require_once('_gate.php');

    $errors = $_SESSION['errors'] ?? [];
    $old    = $_SESSION['old']    ?? [];
    unset($_SESSION['errors'], $_SESSION['old']);

    $pageTitle = "Add Restaurant - Admin";
    $extraScripts = ['../../assets/js/admin.js'];
    include('../header.php');
?>

    <h1>Add Restaurant</h1>
    <p><a href="restaurants.php">&larr; Back to list</a></p>

    <?php if(!empty($errors)){ ?>
        <div class="flash error">
            <?php foreach($errors as $e){ ?>
                <div><?= htmlspecialchars($e) ?></div>
            <?php } ?>
        </div>
    <?php } ?>

    <form method="post" action="../../controller/restaurantCreate.php"
          id="restaurantForm" novalidate>
        <fieldset>
            <legend>New Restaurant</legend>

            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">

            <label for="name">Name</label>
            <input type="text" name="name" id="name"
                   value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>

            <label for="location">Location (city)</label>
            <input type="text" name="location" id="location"
                   value="<?= htmlspecialchars($old['location'] ?? '') ?>" required>

            <label for="area">Area (neighborhood)</label>
            <input type="text" name="area" id="area"
                   value="<?= htmlspecialchars($old['area'] ?? '') ?>" required>

            <label for="short_background">Short Background</label>
            <textarea name="short_background" id="short_background"><?= htmlspecialchars($old['short_background'] ?? '') ?></textarea>

            <label for="goals">Goals</label>
            <textarea name="goals" id="goals"><?= htmlspecialchars($old['goals'] ?? '') ?></textarea>

            <input type="submit" name="submit" value="Create">
        </fieldset>
    </form>

<?php include('../footer.php'); ?>