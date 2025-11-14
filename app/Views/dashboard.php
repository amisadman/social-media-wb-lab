<?php
$title = 'Dashboard | Catgram';
ob_start();
?>

<div class="max-w-2xl mx-auto space-y-6">

  <!-- Composer / Create Post Card -->
  <div class="smooth-card p-6">
    <div class="flex items-center gap-4">
      <!-- User avatar placeholder -->
      <div class="flex-shrink-0 w-10 h-10 rounded-full overflow-hidden">
        <img src="/assets/cat.gif" alt="avatar" class="w-full h-full object-cover" />
      </div>
      
      <!-- Composer input -->
      <a href="/post/create" class="flex-1 px-4 py-3 rounded-full bg-gray-200 text-gray-600 hover:bg-gray-300 transition cursor-pointer font-medium">
        What's on your mind, <?= htmlspecialchars(explode(' ', $user['name'])[0]) ?>?
      </a>
      
      <!-- Create post button -->
      <a href="/post/create" class="btn-primary px-6 py-3 rounded-lg font-semibold text-white transition-all whitespace-nowrap">
        Create Post
      </a>
    </div>
  </div>

  <!-- Posts Feed -->
  <?php if (!empty($posts)): ?>
    <?php foreach ($posts as $post): ?>
      <div class="smooth-card overflow-hidden" data-post="<?= $post['id'] ?>">
        
        <!-- Post Header: Username & Time -->
        <div class="px-6 pt-6 pb-3 border-b border-gray-100">
          <div class="flex items-center justify-between">
            <div>
              <p class="font-bold text-[var(--cat-dark)]"><?= htmlspecialchars($post['name']) ?></p>
              <p class="text-xs text-gray-500 mt-1"><?= date('M d, Y \a\t H:i', strtotime($post['created_at'])) ?></p>
            </div>
          </div>
        </div>

        <!-- Post Content -->
        <div class="px-6 py-4">
          <p class="text-gray-800 leading-relaxed whitespace-pre-wrap"><?= nl2br(htmlspecialchars($post['content'])) ?></p>
        </div>

        <!-- Post Image (centered) -->
        <?php if (!empty($post['image'])): ?>
          <div class="w-full bg-gray-100">
            <img src="/uploads/<?= htmlspecialchars($post['image']) ?>" alt="Post Image" class="w-full h-auto" />
          </div>
        <?php endif; ?>

        <!-- Divider -->
        <div class="border-b border-gray-100"></div>

        <!-- Post Actions (Like/Dislike) -->
        <div class="px-6 py-4 flex items-center gap-4">
          <button class="vote-btn flex items-center justify-center gap-2 flex-1 py-2 rounded-lg hover:bg-gray-100 transition font-medium" data-post-id="<?= $post['id'] ?>" data-value="1" title="Like">
            <img src="/assets/like.png" alt="like" class="w-6 h-6" />
            <span class="like-count"><?= (int)($post['likes'] ?? 0) ?></span>
          </button>

          <button class="vote-btn flex items-center justify-center gap-2 flex-1 py-2 rounded-lg hover:bg-gray-100 transition font-medium" data-post-id="<?= $post['id'] ?>" data-value="-1" title="Dislike">
            <img src="/assets/dislike.png" alt="dislike" class="w-6 h-6" />
            <span class="dislike-count"><?= (int)($post['dislikes'] ?? 0) ?></span>
          </button>

        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="smooth-card p-12 text-center">
      <p class="text-gray-600 text-lg mb-4">No posts yet. Be the first to share something!</p>
      <a href="/post/create" class="btn-primary px-6 py-3 rounded-lg font-semibold text-white transition-all inline-block">Create Your First Post</a>
    </div>
  <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
