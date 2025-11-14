<?php
$q = htmlspecialchars($_GET['q'] ?? '');
$type = $_GET['type'] ?? 'posts';
$mode = $mode ?? ($type === 'users' ? 'users' : 'posts');
$results = $results ?? [];
$title = "Search: $q | Catgram";
ob_start();
?>

<div class="max-w-4xl mx-auto">
  <h2 class="text-2xl font-bold mb-6 text-[var(--cat-primary)]">Search Results for "<em><?php echo $q; ?></em>"</h2>

  <?php if ($mode === 'all'): ?>
    <!-- Posts Section -->
    <div class="mb-8">
      <h3 class="text-xl font-bold mb-4 text-[var(--cat-dark)]">Posts</h3>
      <?php if (!empty($results['posts'])): ?>
        <?php foreach ($results['posts'] as $p): ?>
          <div class="smooth-card p-5 mb-3">
            <a href="/posts/<?php echo (int)$p['id']; ?>" class="text-[var(--cat-primary)] font-medium hover:text-[var(--cat-primary-dark)]"><?php echo htmlspecialchars($p['title'] ?? substr($p['content'],0,60)); ?></a>
            <div class="text-sm text-gray-600 mt-1">By <?php echo htmlspecialchars($p['author_name'] ?? 'Unknown'); ?></div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-gray-600">No posts found.</p>
      <?php endif; ?>
    </div>

    <!-- Users Section -->
    <div>
      <h3 class="text-xl font-bold mb-4 text-[var(--cat-dark)]">Users</h3>
      <?php if (!empty($results['users'])): ?>
        <?php foreach ($results['users'] as $u): ?>
          <div class="smooth-card p-5 mb-3 flex justify-between items-center">
            <div>
              <div class="font-semibold text-[var(--cat-dark)]"><?php echo htmlspecialchars($u['name']); ?></div>
              <div class="text-sm text-gray-600"><?php echo htmlspecialchars($u['email']); ?></div>
            </div>
            <a href="/users/<?php echo (int)$u['id']; ?>" class="btn-primary px-4 py-2 rounded-lg text-white text-sm font-semibold">View</a>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-gray-600">No users found.</p>
      <?php endif; ?>
    </div>

  <?php else: ?>
    <!-- Single Mode Results -->
    <?php if (empty($results)): ?>
      <div class="smooth-card p-6 text-center">
        <p class="text-gray-600">No <?php echo $mode === 'users' ? 'users' : 'posts'; ?> found matching your search.</p>
      </div>
    <?php else: ?>
      <?php if ($mode === 'users'): ?>
        <?php foreach ($results as $u): ?>
          <div class="smooth-card p-5 mb-3 flex justify-between items-center">
            <div>
              <div class="font-semibold text-[var(--cat-dark)]"><?php echo htmlspecialchars($u['name']); ?></div>
              <div class="text-sm text-gray-600"><?php echo htmlspecialchars($u['email']); ?></div>
            </div>
            <a href="/users/<?php echo (int)$u['id']; ?>" class="btn-primary px-4 py-2 rounded-lg text-white text-sm font-semibold">View</a>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <?php foreach ($results as $p): ?>
          <div class="smooth-card p-5 mb-3">
            <a href="/posts/<?php echo (int)$p['id']; ?>" class="text-[var(--cat-primary)] font-medium hover:text-[var(--cat-primary-dark)]"><?php echo htmlspecialchars($p['title'] ?? substr($p['content'],0,60)); ?></a>
            <div class="text-sm text-gray-600 mt-1">By <?php echo htmlspecialchars($p['author_name'] ?? 'Unknown'); ?></div>
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