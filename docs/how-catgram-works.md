# How Catgram Works

This document explains the overall architecture of Catgram, how the MVC pieces fit together, and the database schema used by the application. It is intended to help new contributors quickly understand the project.

## Summary

- Language: PHP (server-rendered pages)
- Pattern: Lightweight MVC (no framework)
- Frontend: Tailwind CSS + DaisyUI via CDN (no JS framework)
- Database: MySQL (schema in `sql/schema.sql`)

The application follows a simple MVC layout: `app/Controllers`, `app/Models`, and `app/Views` with a small core in `app/Core` (router, controller base, session helper, mailer).

## Request flow

1. `public/index.php` bootstraps the app, starts the session, and registers routes using `App\\Core\\Router`.
2. Each route calls a controller action (e.g., `AuthController::showLogin`, `PostController::create`).
3. Controllers use Models to read/write the database and call `$this->view(...)` or `require` a view file from `app/Views`.
4. Views are PHP templates that render HTML and are styled with Tailwind + site CSS.

## Folder map (important parts)

- `public/` — public web root, `index.php`, `assets/`, `uploads/` (user images)
- `app/Core` — Router, Controller base, Session, Mailer
- `app/Controllers` — AuthController, DashboardController, PostController, SearchController
- `app/Models` — User.php, Post.php (PDO-based DB access)
- `app/Views` — PHP templates (layout.php and per-page templates)

## Key components

- Router: maps method+path to a closure/callable. Implemented in `app/Core/Router.php`.
- Controller: base class provides `view()` helper to include templates.
- Session: `app/Core/Session.php` wraps `session_start()` + helpers.

## Database schema

See `sql/schema.sql`. Core tables:

```sql
CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE posts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  content TEXT NOT NULL,
  image VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

Files store uploaded filenames in the `posts.image` field. Actual files are saved under `public/uploads/`.

## Contributing notes

- Follow the existing MVC organization when adding features.
- Use prepared statements (PDO) as in existing Model files to avoid SQL injection.
- Keep presentation in `app/Views` and logic in `app/Controllers` / `app/Models`.

---

For further details, read the feature-specific docs in this folder (login, register, post, dashboard, search, design).
