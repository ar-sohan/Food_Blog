<?php
    session_start();
    require_once('../model/userModel.php');

    // Session gate.
    if(!isset($_SESSION['user_id'])){
        header('location: login.php');
        exit;
    }

    if(!isset($_SESSION['csrf'])){
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    $user   = findUserById($_SESSION['user_id']);
    $flash  = $_SESSION['flash']  ?? null;
    $errors = $_SESSION['errors'] ?? [];
    $old    = $_SESSION['old']    ?? [];
    unset($_SESSION['flash'], $_SESSION['errors'], $_SESSION['old']);

    $pageTitle = "My Profile - Online Food Blog";
    $extraScripts = ['../assets/js/auth.js'];
    include('header.php');
?>

    <h1>My Profile</h1>

    <?php if($flash){ ?>
        <div class="flash <?= $flash['type'] === 'error' ? 'error' : '' ?>">
            <?= htmlspecialchars($flash['msg']) ?>
        </div>
    <?php } ?>

    <?php if(!empty($errors)){ ?>
        <div class="flash error">
            <?php foreach($errors as $e){ ?>
                <div><?= htmlspecialchars($e) ?></div>
            <?php } ?>
        </div>
    <?php } ?>

    <div class="profile-card">
        <?php if(!empty($user['profile_picture'])){ ?>
            <img class="profile-pic"
                 src="../assets/uploads/profile/<?= htmlspecialchars($user['profile_picture']) ?>"
                 alt="Profile picture">
        <?php } else { ?>
            <div class="profile-pic placeholder">No picture</div>
        <?php } ?>
        <div>
            <strong><?= htmlspecialchars($user['name']) ?></strong><br>
            <span><?= htmlspecialchars($user['email']) ?></span><br>
            <span class="role-badge"><?= htmlspecialchars($user['role']) ?></span>
        </div>
    </div>

    <form method="post" action="../controller/profileCheck.php" id="profileForm"
          enctype="multipart/form-data" novalidate>
        <fieldset>
            <legend>Update Profile</legend>

            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">

            <label for="name">Name</label>
            <input type="text" name="name" id="name"
                   value="<?= htmlspecialchars($old['name'] ?? $user['name']) ?>" required>

            <label for="email">Email</label>
            <input type="email" name="email" id="email"
                   value="<?= htmlspecialchars($old['email'] ?? $user['email']) ?>" required>

            <label for="profile_picture">Profile Picture (JPEG or PNG, max 2&nbsp;MB)</label>
            <input type="file" name="profile_picture" id="profile_picture"
                   accept="image/jpeg, image/png">

            <input type="submit" name="submit" value="Save Changes">
        </fieldset>
    </form>

    <form method="post" action="../controller/passwordCheck.php" id="passwordForm" novalidate>
        <fieldset>
            <legend>Change Password</legend>

            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">

            <label for="current_password">Current Password</label>
            <input type="password" name="current_password" id="current_password" required>

            <label for="new_password">New Password (at least 8 characters)</label>
            <input type="password" name="new_password" id="new_password" required>

            <label for="new_password2">Confirm New Password</label>
            <input type="password" name="new_password2" id="new_password2" required>

            <input type="submit" name="submit" value="Change Password">
        </fieldset>
    </form>

<?php include('footer.php'); ?>
