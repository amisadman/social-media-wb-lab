<?php
$q = htmlspecialchars($_GET['q'] ?? '');
$type = $_GET['type'] ?? 'posts';
$mode = $mode ?? ($type === 'users' ? 'users' : 'posts');
$results = $results ?? [];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Search results</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-4xl mx-auto p-6">
    <form action="/search" method="get" class="mb-6">
      <div class="flex gap-2">
        <input name="q" value="<?php echo $q; ?>" placeholder="Search users or posts..." class="flex-1 px-4 py-2 border rounded-lg" />
        <select name="type" class="px-3 py-2 border rounded-lg">
          <option value="posts" <?php echo $type === 'posts' ? 'selected' : ''; ?>>Posts</option>
          <option value="users" <?php echo $type === 'users' ? 'selected' : ''; ?>>Users</option>
          <option value="all" <?php echo $type === 'all' ? 'selected' : ''; ?>>All</option>
        </select>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Search</button>
      </div>
    </form>

    <?php if ($mode === 'all'): ?>
      <h2 class="text-xl font-semibold mb-4">Posts</h2>
      <?php foreach ($results['posts'] as $p): ?>
        <div class="bg-white p-4 rounded mb-3">
          <a href="/posts/<?php echo (int)$p['id']; ?>" class="text-indigo-600 font-medium"><?php echo htmlspecialchars($p['title'] ?? substr($p['content'],0,60)); ?></a>
          <div class="text-sm text-gray-500">By <?php echo htmlspecialchars($p['author_name'] ?? ''); ?></div>
        </div>
      <?php endforeach; ?>

      <h2 class="text-xl font-semibold mt-6 mb-4">Users</h2>
      <?php foreach ($results['users'] as $u): ?>
        <div class="bg-white p-4 rounded mb-3 flex justify-between items-center">
          <div>
            <div class="font-medium"><?php echo htmlspecialchars($u['name']); ?></div>
            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($u['email']); ?></div>
          </div>
          <a href="/users/<?php echo (int)$u['id']; ?>" class="text-indigo-600">View</a>
        </div>
      <?php endforeach; ?>

    <?php else: ?>
      <h2 class="text-2xl mb-4"><?php echo $mode === 'users' ? 'Users' : 'Posts'; ?> matching "<?php echo $q; ?>"</h2>
      <?php if (empty($results)): ?>
        <div class="text-gray-600">No results found.</div>
      <?php else: ?>
        <?php if ($mode === 'users'): ?>
          <?php foreach ($results as $u): ?>
            <div class="bg-white p-4 rounded mb-3 flex justify-between items-center">
              <div>
                <div class="font-medium"><?php echo htmlspecialchars($u['name']); ?></div>
                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($u['email']); ?></div>
              </div>
              <a href="/users/<?php echo (int)$u['id']; ?>" class="text-indigo-600">View</a>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <?php foreach ($results as $p): ?>
            <div class="bg-white p-4 rounded mb-3">
              <a href="/posts/<?php echo (int)$p['id']; ?>" class="text-indigo-600 font-medium"><?php echo htmlspecialchars($p['title'] ?? substr($p['content'],0,60)); ?></a>
              <div class="text-sm text-gray-500">By <?php echo htmlspecialchars($p['author_name'] ?? ''); ?></div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</body>
</html>