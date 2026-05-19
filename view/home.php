<?php
    session_start();
    $pageTitle = "Home - Online Food Blog";
    $extraScripts = ['../assets/js/search.js'];
    include('header.php');
?>

    <section class="hero">
        <h1>Discover Restaurants &amp; Their Best Dishes</h1>
        <p>Browse restaurants, explore menus, and read what other foodies say.</p>
        <?php if(!isset($_SESSION['user_id'])){ ?>
            <p>
                <a class="btn" href="signup.php">Sign up</a>
                <a class="btn btn-secondary" href="login.php">Login</a>
            </p>
        <?php } else { ?>
            <p>Welcome back, <?= htmlspecialchars($_SESSION['name']) ?>!</p>
        <?php } ?>
    </section>

    <?php include('searchBox.php'); ?>

    <section class="quick-links">
        <h2>Get Started</h2>
        <ul>
            <li><a href="restaurants.php">Browse all restaurants</a></li>
            <?php if(!isset($_SESSION['user_id'])){ ?>
                <li><a href="signup.php">Create a member account</a> to post reviews</li>
            <?php } else { ?>
                <li><a href="blog.php">Read member blogs</a></li>
                <li><a href="blogCreate.php">Write a blog</a></li>
            <?php } ?>
        </ul>
    </section>

<?php include('footer.php'); ?>
