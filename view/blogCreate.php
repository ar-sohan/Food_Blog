<?php
    session_start();

    if(!isset($_SESSION['user_id'])){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Please login to write a blog.'];
        header('location: login.php');
        exit;
    }

    if(!isset($_SESSION['csrf'])){
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    $errors = $_SESSION['errors'] ?? [];
    $old    = $_SESSION['old']    ?? [];
    $flash  = $_SESSION['flash']  ?? null;
    unset($_SESSION['errors'], $_SESSION['old'], $_SESSION['flash']);

    $pageTitle = "Write Blog - Online Food Blog";
    include('header.php');
?>

    <h1>Write Blog</h1>
    <p><a href="blog.php">&larr; Back to blogs</a></p>

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

    <form method="post" action="../controller/blogCreate.php" id="blogForm" enctype="multipart/form-data" novalidate>
        <fieldset>
            <legend>New Blog</legend>

            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">

            <label for="title">Title</label>
            <input type="text" name="title" id="title"
                   value="<?= htmlspecialchars($old['title'] ?? '') ?>" required>

            <label for="post_type">Blog Type</label>
            <select name="post_type" id="post_type" required>
                <?php $selectedType = $old['post_type'] ?? 'food'; ?>
                <option value="food" <?= $selectedType === 'food' ? 'selected' : '' ?>>Food</option>
                <option value="restaurant" <?= $selectedType === 'restaurant' ? 'selected' : '' ?>>Restaurant</option>
                <option value="both" <?= $selectedType === 'both' ? 'selected' : '' ?>>Both</option>
            </select>

            <label for="content">Content</label>
            <textarea name="content" id="content" required><?= htmlspecialchars($old['content'] ?? '') ?></textarea>

            <label for="image">Blog Picture</label>
            <input type="file" name="image" id="image" accept="image/jpeg,image/png">

            <input type="submit" name="submit" value="Publish Blog">
        </fieldset>
    </form>

<?php include('footer.php'); ?>
