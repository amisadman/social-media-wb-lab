<?php
$title = 'Dashboard | AuthBoard';
ob_start();
?>

<h2>Welcome, <?= ($user['name']) ?>!</h2>
<p>Your email: <?= ($user['email']) ?></p>

<hr>

<a href="/post/create" class="btn">Create New Post</a>

<hr>

<h3>Recent Posts</h3>

<?php if (!empty($posts)): ?>
    <?php foreach ($posts as $post): ?>
        <div class="post-card">
            <p><strong><?= ($post['name']); ?></strong></p>
            <p><?= nl2br(($post['content'])); ?></p>

            <?php if (!empty($post['image'])): ?>
                <img src="/uploads/<?= ($post['image']); ?>"
                     alt="Post Image"
                     style="max-width:300px; border-radius:10px;">
            <?php endif; ?>

            <small>Posted on <?= ($post['created_at']); ?></small>
        </div>
        <hr>
    <?php endforeach; ?>
<?php else: ?>
    <p>No posts yet. Be the first to post something!</p>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
