<?php
declare(strict_types=1);

// autoload
require __DIR__ . '/../vendor/autoload.php';

// tiny .env loader (reads .env into getenv and $_ENV)
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        [$key, $val] = array_map('trim', explode('=', $line, 2) + [1=>null]);
        if ($key && $val !== null) {
            putenv("$key=$val");
            $_ENV[$key] = $val;
        }
    }
}

use App\Core\Router;
use App\Core\Session;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\PostController;
use \App\Controllers\SearchController;

Session::start();

$router = new Router();
$auth = new AuthController();
$dash = new DashboardController();
$post = new PostController();
$search = new SearchController();

$router->get('/', fn() => include __DIR__ . '/../app/Views/home.php');
$router->get('/home', fn() => include __DIR__ . '/../app/Views/home.php');
// ---- Auth Routes ----
$router->get('/login', fn() => $auth->showLogin());
$router->get('/register', fn() => $auth->showRegister());
$router->post('/login', fn() => $auth->login());
$router->post('/register', fn() => $auth->register());
$router->get('/logout', fn() => $auth->logout());

// ---- Dashboard ----
$router->get('/dashboard', fn() => $dash->index());

// ---- Post Routes ----
$router->get('/post/create', fn() => $post->create());  // shows create_post.php
$router->post('/post/create', fn() => $post->create()); // handles form submission


$router->get('/search', fn() => $search->search());

// ---- Dispatch ----
$router->dispatch($_SERVER['REQUEST_URI'] ?? '/', $_SERVER['REQUEST_METHOD'] ?? 'GET');
