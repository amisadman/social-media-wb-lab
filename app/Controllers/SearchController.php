<?php
namespace App\Controllers;

use App\Models\Post;
use App\Models\User;

class SearchController
{
    public function search()
    {
        $q = trim((string)($_GET['q'] ?? ''));
        $type = strtolower((string)($_GET['type'] ?? 'posts'));
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        if ($q === '') {
            require __DIR__ . '/../Views/search/results.php';
            return;
        }

        if ($type === 'users') {
            $results = User::searchByName($q, $perPage, $offset);
            $mode = 'users';
        } elseif ($type === 'all') {
            $results = [
                'posts' => Post::searchWithAuthor($q, $perPage, $offset),
                'users' => User::searchByName($q, $perPage, $offset)
            ];
            $mode = 'all';
        } else {
            $results = Post::searchWithAuthor($q, $perPage, $offset);
            $mode = 'posts';
        }

        require __DIR__ . '/../Views/search/results.php';
    }
}