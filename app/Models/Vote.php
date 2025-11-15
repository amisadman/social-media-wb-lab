<?php
namespace App\Models;

use PDO;

class Vote {
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

    // Get aggregated counts for multiple post IDs
    public static function getCountsForPostIds(array $postIds): array {
        if (empty($postIds)) return [];
        $placeholders = implode(',', array_fill(0, count($postIds), '?'));
        $sql = "SELECT target_id as post_id,
                    SUM(value = 1) AS likes,
                    SUM(value = -1) AS dislikes
                FROM votes
                WHERE target_type = 'post' AND target_id IN ($placeholders)
                GROUP BY target_id";
        $stmt = self::connect()->prepare($sql);
        $stmt->execute($postIds);
        $rows = $stmt->fetchAll();
        $out = [];
        foreach ($rows as $r) {
            $out[(int)$r['post_id']] = [
                'likes' => (int)$r['likes'],
                'dislikes' => (int)$r['dislikes']
            ];
        }
        return $out;
    }

    // Get the current user's vote for a set of posts
    public static function getUserVotesForPosts(int $userId, array $postIds): array {
        if (empty($postIds)) return [];
        $placeholders = implode(',', array_fill(0, count($postIds), '?'));
        $params = array_merge([$userId], $postIds);
        $sql = "SELECT target_id as post_id, value
                FROM votes
                WHERE user_id = ? AND target_type = 'post' AND target_id IN ($placeholders)";
        $stmt = self::connect()->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
        $out = [];
        foreach ($rows as $r) {
            $out[(int)$r['post_id']] = (int)$r['value'];
        }
        return $out;
    }

    // Cast or toggle a vote for a user on a target
    public static function castVote(int $userId, string $targetType, int $targetId, int $value): array {
        $db = self::connect();
        $db->beginTransaction();
        try {
            // fetch existing
            $sel = $db->prepare('SELECT id, value FROM votes WHERE user_id = ? AND target_type = ? AND target_id = ? FOR UPDATE');
            $sel->execute([$userId, $targetType, $targetId]);
            $existing = $sel->fetch();

            if (!$existing) {
                // insert
                $ins = $db->prepare('INSERT INTO votes (user_id, target_type, target_id, value) VALUES (?, ?, ?, ?)');
                $ins->execute([$userId, $targetType, $targetId, $value]);
            } else {
                if ((int)$existing['value'] === $value) {
                    // same vote - remove (unvote)
                    $del = $db->prepare('DELETE FROM votes WHERE id = ?');
                    $del->execute([$existing['id']]);
                } else {
                    // change
                    $upd = $db->prepare('UPDATE votes SET value = ?, created_at = CURRENT_TIMESTAMP WHERE id = ?');
                    $upd->execute([$value, $existing['id']]);
                }
            }

            // Update cached counters on posts/comments if target_type = 'post' or 'comment'
            if ($targetType === 'post') {
                // recalc counts for this post and update posts.likes_count/dislikes_count
                $countsStmt = $db->prepare("SELECT
                    SUM(value = 1) AS likes,
                    SUM(value = -1) AS dislikes
                    FROM votes
                    WHERE target_type = 'post' AND target_id = ?");
                $countsStmt->execute([$targetId]);
                $counts = $countsStmt->fetch();
                $likes = (int)($counts['likes'] ?? 0);
                $dislikes = (int)($counts['dislikes'] ?? 0);

                $updatePost = $db->prepare('UPDATE posts SET likes_count = ?, dislikes_count = ? WHERE id = ?');
                $updatePost->execute([$likes, $dislikes, $targetId]);
            }

            $db->commit();
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // return fresh counts and the user's current vote
        $counts = self::getCountsForPostIds([$targetId]);
        $userVote = 0;
        $userVotes = self::getUserVotesForPosts($userId, [$targetId]);
        if (isset($userVotes[$targetId])) $userVote = $userVotes[$targetId];

        return [
            'likes' => $counts[$targetId]['likes'] ?? 0,
            'dislikes' => $counts[$targetId]['dislikes'] ?? 0,
            'user_vote' => $userVote
        ];
    }
}
