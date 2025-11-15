<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    if (empty($_POST['id'])) {
        throw new Exception('Video ID is required');
    }

    // Delete the video
    $stmt = $pdo->prepare("DELETE FROM yt_videos WHERE id = ?");
    $result = $stmt->execute([$_POST['id']]);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Video deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete video');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}