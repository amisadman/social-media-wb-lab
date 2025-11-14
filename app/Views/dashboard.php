<?php
$title = 'Dashboard | Catgram';
ob_start();
?>

<div class="max-w-2xl mx-auto">
  <div class="smooth-card p-6 mb-6">
    <div class="flex items-center justify-between gap-4">
      <div>
        <h2 class="text-2xl font-bold text-[var(--cat-primary)]">Welcome, <?= ($user['name']) ?>!</h2>
        <p class="text-sm text-gray-600 mt-1">Your email: <?= ($user['email']) ?></p>
      </div>
      <a href="/post/create" class="btn-primary px-6 py-2 rounded-lg font-semibold text-white transition-all whitespace-nowrap">Create Post</a>
    </div>
  </div>

  <div class="mt-6">
    <h3 class="text-xl font-bold mb-4 text-[var(--cat-dark)]">Recent Posts</h3>

    <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $post): ?>
            <div class="smooth-card p-6 mb-4">
              <div class="flex justify-between items-start gap-4">
                  <div class="flex-1">
                      <p class="font-semibold text-[var(--cat-dark)]"><?= ($post['name']); ?></p>
                      <p class="mt-2 text-sm text-gray-700 whitespace-pre-wrap"><?= nl2br(($post['content'])); ?></p>
                  </div>
                  <?php if (!empty($post['image'])): ?>
                      <div class="flex-shrink-0" style="max-width:200px;">
                          <img src="/uploads/<?= ($post['image']); ?>" alt="Post Image" class="rounded-lg w-full object-cover" />
                      </div>
                  <?php endif; ?>
              </div>
              <div class="mt-3 text-xs text-gray-500">Posted on <?= ($post['created_at']); ?></div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="smooth-card p-6 text-center">
          <p class="text-gray-600">No posts yet. Be the first to <a href="/post/create" class="font-semibold text-[var(--cat-primary)] hover:text-[var(--cat-primary-dark)]">post something</a>!</p>
        </div>
    <?php endif; ?>
  </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
