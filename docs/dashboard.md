# Dashboard (Detailed)

This document explains how the dashboard page works, how posts are fetched, and where to look for controller/view/model code.

## Flow summary

1. The user navigates to `/dashboard`.
2. `public/index.php` maps the route to `App\\Controllers\\DashboardController::index()`.
3. The controller checks the session (must be logged in) and loads posts via `Post::getAllWithUser()`.
4. The controller calls the view `app/Views/dashboard.php` with `user` and `posts` data.

## Key code

DashboardController::index (simplified):

```php
// app/Controllers/DashboardController.php
$user = Session::get('user');
if (!$user) { header('Location: /login'); exit; }

$posts = Post::getAllWithUser();
$this->view('dashboard.php', ['user' => $user, 'posts' => $posts]);
```

Post::getAllWithUser queries posts with the joining user name:

```php
// app/Models/Post.php
SELECT posts.*, users.name
FROM posts
JOIN users ON posts.user_id = users.id
ORDER BY posts.created_at DESC
```

## View

The view `app/Views/dashboard.php` iterates `$posts` and renders each post. Image filenames from the `posts.image` column are rendered as `/uploads/<filename>`.

## Notes for contributors

- To add pagination, change `Post::getAllWithUser()` to support LIMIT/OFFSET and pass page params from the controller.
- To add like/comment features, create `likes` and `comments` tables and extend `Post` queries to include aggregated counts.
