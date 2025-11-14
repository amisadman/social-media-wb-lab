<?php
$q = htmlspecialchars($_GET['q'] ?? '');
$type = $_GET['type'] ?? 'posts';
$mode = $mode ?? ($type === 'users' ? 'users' : 'posts');
$results = $results ?? [];
$title = "Search: $q | Catgram";
ob_start();
?>

<div class="max-w-2xl mx-auto">
  <div class="mb-6">
    <h2 class="text-3xl font-bold text-[var(--cat-dark)]">Search Results</h2>
    <?php if (!empty($q)): ?>
      <p class="text-gray-600 mt-2">Showing results for "<span class="font-semibold"><?= htmlspecialchars($q) ?></span>"</p>
    <?php else: ?>
      <p class="text-gray-600 mt-2">Enter a search query to get started</p>
    <?php endif; ?>
  </div>

  <?php if (empty($q)): ?>
    <div class="smooth-card p-12 text-center">
      <p class="text-gray-600 text-lg">Try searching for posts, users, or tags above</p>
    </div>
  <?php elseif ($mode === 'all'): ?>
    <!-- Posts Section -->
    <div class="mb-8">
      <h3 class="text-xl font-bold mb-4 text-[var(--cat-dark)]">Posts</h3>
      <?php if (!empty($results['posts'])): ?>
        <?php foreach ($results['posts'] as $p): ?>
          <div class="smooth-card overflow-hidden mb-4">
            <div class="px-6 pt-6 pb-3 border-b border-gray-100">
              <p class="font-bold text-[var(--cat-dark)]"><?= htmlspecialchars($p['author_name'] ?? 'Unknown') ?></p>
              <p class="text-xs text-gray-500 mt-1"><?= date('M d, Y \a\t H:i', strtotime($p['created_at'])) ?></p>
            </div>
            <div class="px-6 py-4">
              <p class="text-gray-800 leading-relaxed whitespace-pre-wrap"><?= nl2br(htmlspecialchars(substr($p['content'], 0, 200))) ?></p>
              <?php if (strlen($p['content']) > 200): ?>
                <p class="text-gray-600 font-medium mt-2">... more</p>
              <?php endif; ?>
            </div>
            <?php if (!empty($p['image'])): ?>
              <div class="w-full bg-gray-100">
                <img src="/uploads/<?= htmlspecialchars($p['image']) ?>" alt="Post Image" class="w-full h-auto" />
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-gray-600 text-center py-4">No posts found.</p>
      <?php endif; ?>
    </div>

    <!-- Users Section -->
    <div>
      <h3 class="text-xl font-bold mb-4 text-[var(--cat-dark)]">Users</h3>
      <?php if (!empty($results['users'])): ?>
        <?php foreach ($results['users'] as $u): ?>
          <div class="smooth-card p-5 mb-3 flex justify-between items-center">
            <div>
              <div class="font-semibold text-[var(--cat-dark)]"><?= htmlspecialchars($u['name']) ?></div>
              <div class="text-sm text-gray-600"><?= htmlspecialchars($u['email']) ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-gray-600 text-center py-4">No users found.</p>
      <?php endif; ?>
    </div>

  <?php else: ?>
    <!-- Single Mode Results -->
    <?php if (empty($results)): ?>
      <div class="smooth-card p-12 text-center">
        <p class="text-gray-600 text-lg">No <?= $mode === 'users' ? 'users' : 'posts' ?> found matching your search.</p>
      </div>
    <?php else: ?>
      <?php if ($mode === 'users'): ?>
        <?php foreach ($results as $u): ?>
          <div class="smooth-card p-5 mb-3 flex justify-between items-center">
            <div>
              <div class="font-semibold text-[var(--cat-dark)]"><?= htmlspecialchars($u['name']) ?></div>
              <div class="text-sm text-gray-600"><?= htmlspecialchars($u['email']) ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <?php foreach ($results as $p): ?>
          <div class="smooth-card overflow-hidden mb-4">
            <div class="px-6 pt-6 pb-3 border-b border-gray-100">
              <p class="font-bold text-[var(--cat-dark)]"><?= htmlspecialchars($p['author_name'] ?? 'Unknown') ?></p>
              <p class="text-xs text-gray-500 mt-1"><?= date('M d, Y \a\t H:i', strtotime($p['created_at'])) ?></p>
            </div>
            <div class="px-6 py-4">
              <p class="text-gray-800 leading-relaxed whitespace-pre-wrap"><?= nl2br(htmlspecialchars(substr($p['content'], 0, 200))) ?></p>
              <?php if (strlen($p['content']) > 200): ?>
                <p class="text-gray-600 font-medium mt-2">... more</p>
              <?php endif; ?>
            </div>
            <?php if (!empty($p['image'])): ?>
              <div class="w-full bg-gray-100">
                <img src="/uploads/<?= htmlspecialchars($p['image']) ?>" alt="Post Image" class="w-full h-auto" />
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    <?php endif; ?>
  <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>