<?php
    session_start();

    if(!isset($_SESSION['user_id'])){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Please login to edit blogs.'];
        header('location: login.php');
        exit;
    }

    require_once('../model/blogModel.php');

    if(!isset($_SESSION['csrf'])){
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $blog = $id > 0 ? getBlogPostById($id) : null;

    if(!$blog){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Blog not found.'];
        header('location: blog.php');
        exit;
    }

    if((int)$blog['user_id'] !== (int)$_SESSION['user_id']){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'You can only edit your own blog.'];
        header('location: blogDetail.php?id=' . (int)$blog['id']);
        exit;
    }

    $errors = $_SESSION['errors'] ?? [];
    $old    = $_SESSION['old']    ?? [];
    $flash  = $_SESSION['flash']  ?? null;
    unset($_SESSION['errors'], $_SESSION['old'], $_SESSION['flash']);

    $pageTitle = "Edit Blog - Online Food Blog";
    include('header.php');
?>

    <h1>Edit Blog</h1>
    <p><a href="blogDetail.php?id=<?= (int)$blog['id'] ?>">&larr; Back to blog</a></p>

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

    <form method="post" action="../controller/blogUpdate.php" id="blogForm" enctype="multipart/form-data" novalidate>
        <fieldset>
            <legend>Edit "<?= htmlspecialchars($blog['title']) ?>"</legend>

            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <input type="hidden" name="id" value="<?= (int)$blog['id'] ?>">

            <label for="title">Title</label>
            <input type="text" name="title" id="title"
                   value="<?= htmlspecialchars($old['title'] ?? $blog['title']) ?>" required>

            <label for="post_type">Blog Type</label>
            <?php $selectedType = $old['post_type'] ?? $blog['post_type']; ?>
            <select name="post_type" id="post_type" required>
                <option value="food" <?= $selectedType === 'food' ? 'selected' : '' ?>>Food</option>
                <option value="restaurant" <?= $selectedType === 'restaurant' ? 'selected' : '' ?>>Restaurant</option>
                <option value="both" <?= $selectedType === 'both' ? 'selected' : '' ?>>Both</option>
            </select>

            <label for="content">Content</label>
            <textarea name="content" id="content" required><?= htmlspecialchars($old['content'] ?? $blog['content']) ?></textarea>

            <?php if(!empty($blog['image_path'])){ ?>
                <p class="muted">Current picture</p>
                <img class="blog-form-image" src="../assets/uploads/blog/<?= htmlspecialchars($blog['image_path']) ?>"
                     alt="<?= htmlspecialchars($blog['title']) ?>">
            <?php } ?>

            <label for="image">Change Blog Picture</label>
            <input type="file" name="image" id="image" accept="image/jpeg,image/png">

            <input type="submit" name="submit" value="Save Changes">
        </fieldset>
    </form>

<?php include('footer.php'); ?>
