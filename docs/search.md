# Search (Detailed)

This document explains how the search feature works, where the code is, and how search queries are executed.

## Flow summary

1. The search form sends a GET request to `/search` with parameters `q` and optional `type` (`posts`, `users`, or `all`).
2. `public/index.php` maps to `App\\Controllers\\SearchController::search()`.
3. `SearchController` decides which model method to call:
   - `User::searchByName($q)` for users,
   - `Post::searchWithAuthor($q)` for posts,
   - or both for `all`.
4. Results are passed to `app/Views/search/results.php` which renders posts/users accordingly.

## Key code (controller)

```php
// app/Controllers/SearchController.php (simplified)
$q = trim((string)($_GET['q'] ?? ''));
$type = strtolower((string)($_GET['type'] ?? 'posts'));
if ($q === '') { require __DIR__.'/../Views/search/results.php'; return; }
if ($type === 'users') {
    $results = User::searchByName($q, $perPage, $offset);
} elseif ($type === 'all') {
    $results = [
        'posts' => Post::searchWithAuthor($q, $perPage, $offset),
        'users' => User::searchByName($q, $perPage, $offset)
    ];
} else {
    $results = Post::searchWithAuthor($q, $perPage, $offset);
}
require __DIR__.'/../Views/search/results.php';
```

## Key code (models)

`User::searchByName` safely binds parameters and supports pagination. `Post::searchWithAuthor` searches `posts.content` and user fields and returns post + author metadata.

Example snippet from `Post::searchWithAuthor` (simplified):

```php
$sql = 'SELECT posts.id, posts.content, posts.image, posts.created_at, users.name AS author_name
        FROM posts LEFT JOIN users ON posts.user_id = users.id
        WHERE posts.content LIKE :term OR users.name LIKE :term OR users.email LIKE :term
        ORDER BY posts.created_at DESC LIMIT :limit OFFSET :offset';
```

## Notes and improvements

- Current search uses SQL LIKE which is acceptable for small datasets. For larger datasets consider integrating full-text search (MySQL FULLTEXT) or external search (Elasticsearch, Meilisearch).
- Ensure proper escaping and prepared statements (already used in model methods) to avoid SQL injection.
