<?php 
    if(session_status() == PHP_SESSION_NONE){
        session_start();
    }

    $flash  = $_SESSION['flash']  ?? null;
    $errors = $_SESSION['errors'] ?? [];
    $old    = $_SESSION['old']    ?? [];
    unset($_SESSION['flash'], $_SESSION['errors'], $_SESSION['old']);

    $pageTitle = "Login - Online Food Blog";
    include('header.php');
?>

    <div class="form-container">
        <h2>Login</h2>

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

        <form action="../controller/logincheck.php" method="POST" id="loginForm">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Enter your email"
                       value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                <span class="error-msg" id="emailError"></span>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password">
                <span class="error-msg" id="passwordError"></span>
            </div>
            <div class="form-group remember-group"> 
                <input type="checkbox" name="remember_me" id="remember_me">
                <label for="remember_me">Remember me </label>
            </div>
            <button type="submit" name="login" class="btn-primary">Login</button>
            <p class="form-footer">Don't have an account? <a href="signup.php">Register here</a></p>
        </form>
    </div>

<?php include('footer.php'); ?>
