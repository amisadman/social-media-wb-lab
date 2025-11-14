-- Migration: create votes table and cached counters on posts
ALTER TABLE posts
  ADD COLUMN likes_count INT UNSIGNED NOT NULL DEFAULT 0,
  ADD COLUMN dislikes_count INT UNSIGNED NOT NULL DEFAULT 0;

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
);
