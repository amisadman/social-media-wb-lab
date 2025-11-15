# Like and Dislike — Implementation Guide

This document describes the design and implementation of the Like / Dislike feature for Catgram. It covers database schema, migration SQL, server-side model/controller APIs, view markup, client-side interactions, testing, and recommendations for future extensions (comments, moderation, analytics).

**Intended audience:** backend and frontend developers working on Catgram who will implement, test, or extend the voting system.

--

## Summary and goals

- Provide per-user likes and dislikes for posts (and easily extend to comments).
- Prevent duplicate votes from the same user for the same target.
- Allow toggling (unvote) and switching (like -> dislike) of votes.
- Make it easy to query counts and list who liked a post.
- Keep UI responsive with an AJAX endpoint and optional cached counters for fast reads.

Design choice: We use a normalized, polymorphic `votes` table as the source-of-truth and maintain cached counters on the `posts` table for fast list rendering. This design is extensible to comments and replies.

## Database schema

Recommended polymorphic `votes` table:

```sql
CREATE TABLE votes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  target_type ENUM('post','comment') NOT NULL DEFAULT 'post',
  target_id INT UNSIGNED NOT NULL,
  value TINYINT NOT NULL, -- 1 = like, -1 = dislike
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY ux_user_target (user_id, target_type, target_id),
  INDEX idx_target (target_type, target_id),
  INDEX idx_user (user_id),
  CONSTRAINT fk_votes_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

Keep cached counters on votable tables (example columns on `posts`):

```sql
ALTER TABLE posts
  ADD COLUMN likes_count INT UNSIGNED NOT NULL DEFAULT 0,
  ADD COLUMN dislikes_count INT UNSIGNED NOT NULL DEFAULT 0;
```

Notes:
- `UNIQUE KEY ux_user_target` enforces one vote per user per target.
- `value` stores 1 or -1 to support like/dislike semantics compactly.
- `target_type` lets one table support posts and comments. If you prefer simpler queries for each type, you can create separate `post_votes` and `comment_votes` tables instead.

## Migration example (single file)

Place the following in `sql/your_migration.sql` or run in phpMyAdmin (after selecting your DB):

```sql
-- add cached counters to posts
ALTER TABLE posts
  ADD COLUMN likes_count INT UNSIGNED NOT NULL DEFAULT 0,
  ADD COLUMN dislikes_count INT UNSIGNED NOT NULL DEFAULT 0;

-- create votes table
CREATE TABLE IF NOT EXISTS votes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  target_type ENUM('post','comment') NOT NULL DEFAULT 'post',
  target_id INT UNSIGNED NOT NULL,
  value TINYINT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY ux_user_target (user_id, target_type, target_id),
  INDEX idx_target (target_type, target_id),
  INDEX idx_user (user_id),
  CONSTRAINT fk_votes_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

Compatibility note: Some MySQL versions do not support `ADD COLUMN IF NOT EXISTS`. If you previously had `IF NOT EXISTS` in your SQL and phpMyAdmin returned a syntax error, remove `IF NOT EXISTS` and run again. Always back up your DB first.

## Server-side API (PHP)

We recommend implementing a small controller endpoint `POST /vote` that accepts JSON like:

```json
{ "target_type": "post", "target_id": 123, "value": 1 }
```

- `target_type`: `post` or `comment`
- `target_id`: numeric id of the post/comment
- `value`: 1 for like, -1 for dislike

Server behavior (summary):

1. Authenticate the request (session-based) — return 401 if unauthenticated.
2. Validate inputs (target_type in allowed list, target_id > 0, value ∈ {1,-1}).
3. Use a transaction and `SELECT ... FOR UPDATE` on the `votes` row for `(user_id, target_type, target_id)` to avoid races.
4. If no existing vote: INSERT row with `value`.
5. If existing vote has same `value`: DELETE the row (unvote).
6. If existing vote has different `value`: UPDATE the row to the new `value`.
7. Recalculate aggregated counts for the target (or increment/decrement cached counters consistently) and return the counts and the user's current vote.

Example PHP (conceptual):

```php
// in app/Controllers/VoteController.php
Session::start();
$user = Session::get('user');
if (!$user) { http_response_code(401); exit; }

$input = json_decode(file_get_contents('php://input'), true);
$targetType = $input['target_type'] ?? 'post';
$targetId = (int)($input['target_id'] ?? 0);
$value = (int)($input['value'] ?? 0);

// validate
if (!in_array($targetType, ['post','comment']) || !in_array($value, [1,-1]) || $targetId <= 0) {
  http_response_code(400);
  echo json_encode(['error' => 'Invalid input']);
  exit;
}

// call model
$result = \App\Models\Vote::castVote((int)$user['id'], $targetType, $targetId, $value);
echo json_encode($result);
```

The `Vote::castVote` model should perform the transaction and update cached counts on the related table (e.g., `posts`) if desired.

## Model responsibilities

- `getCountsForPostIds(array $ids)` — return likes/dislikes for a list of posts (used to batch for page rendering).
- `getUserVotesForPosts(int $userId, array $postIds)` — return the user's vote per post for the current page.
- `castVote(int $userId, string $targetType, int $targetId, int $value)` — transactional cast/toggle logic and cached counter updates.

## View markup examples (Tailwind / current project)

Place the like/dislike buttons near the post content. Use `data-*` attributes for JS to locate the target.

Example (in `app/Views/dashboard.php` or post partial):

```php
<div data-post="<?= $post['id'] ?>">
  <button class="vote-btn" data-post-id="<?= $post['id'] ?>" data-value="1">
    <img src="/assets/like.png" alt="like" class="w-5 h-5" />
    <span class="like-count"><?= (int)$post['likes'] ?></span>
  </button>

  <button class="vote-btn" data-post-id="<?= $post['id'] ?>" data-value="-1">
    <img src="/assets/dislike.png" alt="dislike" class="w-5 h-5" />
    <span class="dislike-count"><?= (int)$post['dislikes'] ?></span>
  </button>

  <span class="your-vote-text"><?= ($post['user_vote'] === 1 ? 'Liked' : ($post['user_vote'] === -1 ? 'Disliked' : '')) ?></span>
</div>
```

Use small, clear icons in `public/assets/like.png` and `public/assets/dislike.png`. The project already references these image paths.

## Client-side JS

`public/assets/votes.js` can handle button clicks, post JSON to `/vote`, and update counts optimistically.

Key points:
- Disable the button while the request is in-flight to prevent duplicate clicks.
- Handle HTTP 401 by redirecting to `/login` or showing a prompt.
- On success, update `.like-count`, `.dislike-count`, and the `your-vote-text` content.
- On error, show a friendly message and restore UI state.

Example (simplified):

```js
document.addEventListener('click', function(e){
  const btn = e.target.closest('.vote-btn'); if (!btn) return;
  const postId = btn.dataset.postId; const value = parseInt(btn.dataset.value, 10);
  fetch('/vote', { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({target_type:'post', target_id:postId, value}) })
    .then(r=>r.json())
    .then(data => { /* update counters */ })
    .catch(err => { /* show error */ });
});
```

## Queries you will use

- Counts for one post:
  ```sql
  SELECT
    SUM(value = 1) AS likes,
    SUM(value = -1) AS dislikes
  FROM votes
  WHERE target_type='post' AND target_id = :postId;
  ```

- Counts for many posts (batch):
  ```sql
  SELECT target_id, SUM(value = 1) AS likes, SUM(value = -1) AS dislikes
  FROM votes
  WHERE target_type='post' AND target_id IN (?,?,...)
  GROUP BY target_id;
  ```

- List users who liked a post (paginated):
  ```sql
  SELECT u.id, u.name, v.created_at
  FROM votes v
  JOIN users u ON u.id = v.user_id
  WHERE v.target_type='post' AND v.target_id = :postId AND v.value = 1
  ORDER BY v.created_at DESC
  LIMIT 50 OFFSET 0;
  ```

## Caching and performance recommendations

- Use the cached `likes_count` / `dislikes_count` columns on `posts` for list pages. Update them transactionally in `castVote` to keep them in sync.
- For heavy write traffic, consider using Redis counters and background reconciliation to avoid hot-row contention.
- For the “who liked” pages or analytics, use direct queries on `votes` with proper indexes and pagination.

## Concurrency and race conditions

- Use transactions with `SELECT ... FOR UPDATE` on the single `votes` row to prevent race conditions for the same user+target.
- If you use cached counters on `posts`, update them in the same transaction after mutating `votes` to ensure consistency.

## Security and abuse mitigation

- Validate requests server-side — do not trust client `user_id` values.
- Enforce authentication on `/vote` endpoint.
- Add rate-limiting to `/vote` if you see abusive automated voting.
- Consider storing `ip` and `user_agent` (optional columns) for moderation and anti-abuse analysis.

## Moderation and privacy

- Admins should be able to remove abusive votes (delete from `votes`). If you need audit, consider `vote_history` or soft-deletes.
- If privacy requirements change, you can restrict who can view the full who-liked list (e.g., only friends).

## Extending to comments

- `target_type='comment'` supports comment votes with the same table. Add cached counters to `comments` table (`likes_count` / `dislikes_count`) and update `Vote::castVote` to update either `posts` or `comments` based on `target_type`.

## Testing checklist

- Unit tests for `Vote::castVote` covering: first vote, toggle off, change vote, invalid inputs.
- Integration test for `/vote` endpoint: unauthorized request returns 401; valid request returns JSON with counts.
- Manual tests: login, like post A, refresh page, counts persist; like then dislike toggles counts correctly.

## Rollback and cleanup

- To remove the feature and schema changes:
  ```sql
  DROP TABLE IF EXISTS votes;
  ALTER TABLE posts DROP COLUMN likes_count, DROP COLUMN dislikes_count;
  ```

--