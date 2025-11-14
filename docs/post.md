# Post / Upload (Detailed)

This document explains how creating a post works, how uploads are handled, what is stored in the database, and where files live on disk.

## Flow summary

1. The Create Post form (`app/Views/create_post.php`) submits a `POST /post/create` with `multipart/form-data`.
2. `public/index.php` routes the request to `App\\Controllers\\PostController::create()`.
3. Controller checks the user session, validates the uploaded file (extension), moves the uploaded file to `public/uploads/` with a unique name, and saves the filename in the `posts.image` DB column.

## Key code (file handling) — excerpt from `PostController::create()`

```php
// app/Controllers/PostController.php (excerpt)
if (!empty($_FILES['image']['name'])) {
    $uploadDir = __DIR__ . '/../../public/uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $allowedExt = ['jpg','jpeg','png','gif'];
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    if (in_array($ext, $allowedExt)) {
        $imageName = time() . '_' . preg_replace("/[^a-zA-Z0-9_-]/", "", basename($_FILES['image']['name']));
        $targetFile = $uploadDir . $imageName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = $imageName; // store filename in DB
        }
    }
}

Post::create($user['id'], $content, $imagePath);
```

### Where files are stored

- Files are stored in the project at `public/uploads/<filename>`.
- The `posts.image` DB column stores only the filename (not the full path). When rendering images, views prepend `/uploads/` to the stored filename.

### Database schema (excerpt)

```sql
CREATE TABLE posts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  content TEXT NOT NULL,
  image VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### Security and validation

- Only naive extension checks are performed. For production, validate MIME type (e.g., via `finfo_file`) and consider scanning for malware.
- Set appropriate permissions on `public/uploads/` and never allow uploads directly into code directories.
- Limit upload size in PHP ini (`upload_max_filesize`, `post_max_size`) and optionally check `$_FILES['image']['size']` server-side.

### Serving uploads

- Files in `public/uploads/` are directly accessible. When showing an image in a view use:

```php
<img src="/uploads/<?= htmlspecialchars(
    $post['image']
) ?>" alt="Post image" />
```

---

If you want to support thumbnails, image resizing, or CDN upload, add an image processing step after `move_uploaded_file()` (e.g., `gd` or `imagick`) and consider storing derivative filenames.
