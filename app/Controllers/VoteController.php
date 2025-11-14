<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Vote;

class VoteController extends Controller {
    public function vote(): void {
        Session::start();
        $user = Session::get('user');
        if (!$user) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: [];
        $targetType = $input['target_type'] ?? 'post';
        $targetId = (int)($input['target_id'] ?? 0);
        $value = (int)($input['value'] ?? 0);

        if (!in_array($targetType, ['post','comment'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid target type']);
            return;
        }

        if (!in_array($value, [1, -1])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid vote value']);
            return;
        }

        try {
            $res = Vote::castVote((int)$user['id'], $targetType, $targetId, $value);
            header('Content-Type: application/json');
            echo json_encode($res);
        } catch (\Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Server error', 'message' => $e->getMessage()]);
        }
    }
}
