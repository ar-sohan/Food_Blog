<?php
    session_start();

    // Generate a CSRF token once per session and reuse it.
    if(!isset($_SESSION['csrf'])){
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    // Pull flash + form errors from the previous redirect, then clear them.
    $flash  = $_SESSION['flash']  ?? null;
    $errors = $_SESSION['errors'] ?? [];
    $old    = $_SESSION['old']    ?? [];
    unset($_SESSION['flash'], $_SESSION['errors'], $_SESSION['old']);

    $pageTitle = "Signup - Online Food Blog";
    include('header.php');
?>

    <h1>Create Your Account</h1>

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

    <form method="post" action="../controller/signupCheck.php" id="signupForm" novalidate>
        <fieldset>
            <legend>Signup</legend>

            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">

            <label for="name">Name</label>
            <input type="text" name="name" id="name"
                   value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>

            <label for="email">Email</label>
            <input type="email" name="email" id="email"
                   value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
            <div id="emailStatus" class="error"></div>

            <label for="password">Password (at least 8 characters)</label>
            <input type="password" name="password" id="password" required>

            <label for="password2">Confirm Password</label>
            <input type="password" name="password2" id="password2" required>

            <label for="role">Role</label>
            <select name="role" id="role">
                <option value="member" <?= ($old['role'] ?? 'member') === 'member' ? 'selected' : '' ?>>Member</option>
                <option value="admin"  <?= ($old['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>

            <div id="formErrors" class="error"></div>

            <input type="submit" name="submit" value="Signup">
        </fieldset>
    </form>

    <p>Already have an account? <a href="login.php">Login</a></p>

<?php include('footer.php'); ?>
