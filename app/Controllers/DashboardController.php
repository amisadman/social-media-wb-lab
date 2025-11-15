<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Post;
use App\Models\Vote;

class DashboardController extends Controller {
    public function index(): void
    {
        $user = Session::get('user');
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $posts = Post::getAllWithUser();

        // fetch vote counts and user votes for these posts
        $postIds = array_map(function($p){ return (int)$p['id']; }, $posts);
        $counts = [];
        $userVotes = [];
        if (!empty($postIds)) {
            $counts = Vote::getCountsForPostIds($postIds);
            $userVotes = Vote::getUserVotesForPosts((int)$user['id'], $postIds);
        }

        // merge into posts array for view convenience
        foreach ($posts as &$p) {
            $id = (int)$p['id'];
            $p['likes'] = $counts[$id]['likes'] ?? (int)($p['likes_count'] ?? 0);
            $p['dislikes'] = $counts[$id]['dislikes'] ?? (int)($p['dislikes_count'] ?? 0);
            $p['user_vote'] = $userVotes[$id] ?? 0;
        }

        $this->view('dashboard.php', [
            'user' => $user,
            'posts' => $posts
        ]);
    }
}
