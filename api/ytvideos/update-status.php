<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    if (!isset($_POST['id']) || !isset($_POST['status'])) {
        throw new Exception('Invalid request parameters');
    }

    $stmt = $pdo->prepare("UPDATE yt_videos SET status = ?, updated_at = NOW() WHERE id = ?");
    $result = $stmt->execute([$_POST['status'], $_POST['id']]);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Video status updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update video status');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}