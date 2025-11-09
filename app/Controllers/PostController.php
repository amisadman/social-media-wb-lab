<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Post;

class PostController extends Controller {
    public function create(): void
    {
        $user = Session::get('user');
        if (!$user) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = trim($_POST['content']);
            $imagePath = null;

            if (!empty($_FILES['image']['name'])) {
                $uploadDir = __DIR__ . '/../../public/uploads/';

                // Create directory if not exists
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Only allow image extensions
                $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

                if (in_array($ext, $allowedExt)) {
                    // Give a unique name to prevent overwriting
                    $imageName = time() . '_' . preg_replace("/[^a-zA-Z0-9_-]/", "", basename($_FILES['image']['name']));
                    $targetFile = $uploadDir . $imageName;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                        $imagePath = $imageName; // Store filename in DB
                    }
                }
            }

            // Create the post
            Post::create($user['id'], $content, $imagePath);

            // Redirect to dashboard
            header('Location: /dashboard');
            exit;
        }

        // Show form
        $this->view('create_post.php', ['user' => $user]);
    }
}
