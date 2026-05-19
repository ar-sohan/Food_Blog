<?php
    session_start();

    if(!isset($_SESSION['user_id'])){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Please login to view blogs.'];
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
        $pageTitle = "Blog not found";
        include('header.php');
        echo '<h1>Blog not found</h1>';
        echo '<p><a href="blog.php">Back to blogs</a></p>';
        include('footer.php');
        exit;
    }

    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    $isOwner = (int)$blog['user_id'] === (int)$_SESSION['user_id'];
    $isAdmin = ($_SESSION['role'] ?? '') === 'admin';

    $pageTitle = htmlspecialchars($blog['title']) . " - Online Food Blog";
    include('header.php');
?>

    <p><a href="blog.php">&larr; Back to blogs</a></p>

    <?php if($flash){ ?>
        <div class="flash <?= $flash['type'] === 'error' ? 'error' : '' ?>">
            <?= htmlspecialchars($flash['msg']) ?>
        </div>
    <?php } ?>

    <article class="blog-detail">
        <div class="blog-meta">
            <span><?= htmlspecialchars(ucfirst($blog['post_type'])) ?></span>
            <span><?= htmlspecialchars(date('M d, Y', strtotime($blog['created_at']))) ?></span>
        </div>
        <h1><?= htmlspecialchars($blog['title']) ?></h1>
        <p class="muted">By <?= htmlspecialchars($blog['author_name']) ?></p>
        <?php if(!empty($blog['image_path'])){ ?>
            <img class="blog-hero-image" src="../assets/uploads/blog/<?= htmlspecialchars($blog['image_path']) ?>"
                 alt="<?= htmlspecialchars($blog['title']) ?>">
        <?php } ?>
        <div class="blog-content">
            <?= nl2br(htmlspecialchars($blog['content'])) ?>
        </div>

        <?php if($isOwner || $isAdmin){ ?>
            <div class="blog-actions">
                <?php if($isOwner){ ?>
                    <a class="btn btn-small" href="blogEdit.php?id=<?= (int)$blog['id'] ?>">Edit Blog</a>
                <?php } ?>

                <form class="inline-form" method="post" action="../controller/blogDelete.php"
                      onsubmit="return confirm('Delete this blog?');">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                    <input type="hidden" name="id" value="<?= (int)$blog['id'] ?>">
                    <button class="btn btn-small btn-danger" type="submit" name="submit" value="1">Delete Blog</button>
                </form>
            </div>
        <?php } ?>
    </article>

<?php include('footer.php'); ?>
