<?php
namespace App\Models;

use PDO;

class Post {
    // Private connection method (same pattern as User)
    private static function connect(): PDO {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $db = getenv('DB_NAME') ?: 'authboard';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        return $pdo;
    }

    // Create a new post
    public static function create(int $userId, string $content, ?string $image = null): bool {
        $stmt = self::connect()->prepare('INSERT INTO posts (user_id, content, image) VALUES (?, ?, ?)');
        return $stmt->execute([$userId, $content, $image]);
    }

    // Fetch all posts with user info (newest first)
    public static function getAllWithUser(): array {
        $stmt = self::connect()->query('
            SELECT posts.*, users.name 
            FROM posts 
            JOIN users ON posts.user_id = users.id 
            ORDER BY posts.created_at DESC
        ');
        return $stmt->fetchAll();
    }

    // Fetch all posts by a specific user (optional)
    public static function getByUser(int $userId): array {
        $stmt = self::connect()->prepare('
            SELECT posts.*, users.name 
            FROM posts 
            JOIN users ON posts.user_id = users.id 
            WHERE user_id = ? 
            ORDER BY posts.created_at DESC
        ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    // Delete a post by its ID (optional feature)
    public static function delete(int $postId, int $userId): bool {
        $stmt = self::connect()->prepare('DELETE FROM posts WHERE id = ? AND user_id = ?');
        return $stmt->execute([$postId, $userId]);
    }
    //search posts by name or email 
     public static function searchByName(string $term, int $limit = 50, int $offset = 0): array
    {
        $db = self::connect();
        $sql = '
            SELECT id, name, email
            FROM users
            WHERE name LIKE :term OR email LIKE :term
            ORDER BY name
            LIMIT :limit OFFSET :offset
        ';
        $stmt = $db->prepare($sql);
        $like = '%' . $term . '%';
        $stmt->bindValue(':term', $like, PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    //search posts and include author info
    public static function searchWithAuthor(string $term, int $limit = 50, int $offset = 0): array
    {
        $db = self::connect();
        $sql = '
            SELECT
                posts.id,
                posts.user_id,
                posts.content,
                posts.image,
                posts.created_at,
                users.id AS author_id,
                users.name AS author_name,
                users.email AS author_email
            FROM posts
            LEFT JOIN users ON posts.user_id = users.id
            WHERE posts.content LIKE :term OR users.name LIKE :term OR users.email LIKE :term
            ORDER BY posts.created_at DESC
            LIMIT :limit OFFSET :offset
        ';
        $stmt = $db->prepare($sql);
        $like = '%' . $term . '%';
        $stmt->bindValue(':term', $like, PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
