<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        throw new Exception('Notice ID is required');
    }

    $noticeId = $_POST['id'];

    // Delete the notice
    $stmt = $pdo->prepare("DELETE FROM notices WHERE id = ?");
    $result = $stmt->execute([$noticeId]);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Notice deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete notice');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}