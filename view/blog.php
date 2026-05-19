<?php
    session_start();

    if(!isset($_SESSION['user_id'])){
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Please login to view blogs.'];
        header('location: login.php');
        exit;
    }

    require_once('../model/blogModel.php');

    function shortBlogText($text, $limit = 260){
        $text = $text ?? '';
        if(function_exists('mb_strimwidth')){
            return mb_strimwidth($text, 0, $limit, '...');
        }
        return strlen($text) > $limit ? substr($text, 0, $limit) . '...' : $text;
    }

    $blogs = getAllBlogPosts();
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    $pageTitle = "Blog - Online Food Blog";
    include('header.php');
?>

    <h1>Blog</h1>
    <p class="muted">Read food experiences shared by members.</p>
    <p><a class="btn" href="blogCreate.php">Write Blog</a></p>

    <?php if($flash){ ?>
        <div class="flash <?= $flash['type'] === 'error' ? 'error' : '' ?>">
            <?= htmlspecialchars($flash['msg']) ?>
        </div>
    <?php } ?>

    <?php if(empty($blogs)){ ?>
        <section class="empty-state">
            <h2>No blogs yet</h2>
            <p>Member food stories will appear here after they are posted.</p>
        </section>
    <?php } else { ?>
        <div class="blog-list">
            <?php foreach($blogs as $blog){ ?>
                <article class="blog-post">
                    <?php if(!empty($blog['image_path'])){ ?>
                        <a href="blogDetail.php?id=<?= (int)$blog['id'] ?>">
                            <img class="blog-card-image" src="../assets/uploads/blog/<?= htmlspecialchars($blog['image_path']) ?>"
                                 alt="<?= htmlspecialchars($blog['title']) ?>">
                        </a>
                    <?php } ?>
                    <div class="blog-meta">
                        <span><?= htmlspecialchars(ucfirst($blog['post_type'])) ?></span>
                        <span><?= htmlspecialchars(date('M d, Y', strtotime($blog['created_at']))) ?></span>
                    </div>
                    <h2><a href="blogDetail.php?id=<?= (int)$blog['id'] ?>"><?= htmlspecialchars($blog['title']) ?></a></h2>
                    <p class="muted">By <?= htmlspecialchars($blog['author_name']) ?></p>
                    <p><?= nl2br(htmlspecialchars(shortBlogText($blog['content'], 360))) ?></p>
                    <p><a class="btn btn-small" href="blogDetail.php?id=<?= (int)$blog['id'] ?>">Read Blog</a></p>
                </article>
            <?php } ?>
        </div>
    <?php } ?>

<?php include('footer.php'); ?>
