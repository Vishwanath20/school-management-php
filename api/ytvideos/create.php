<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    // Validate required fields
    if (empty($_POST['title'])) throw new Exception('Title is required');
    if (empty($_POST['subtitle'])) throw new Exception('Subtitle is required');
    if (empty($_POST['youtube_url'])) throw new Exception('YouTube URL is required');
    if (empty($_POST['youtube_id'])) throw new Exception('Invalid YouTube URL format');

    // Insert video
    $stmt = $pdo->prepare("
        INSERT INTO yt_videos (
            title, subtitle, youtube_url, youtube_id,
            display_order, created_at
        ) VALUES (?, ?, ?, ?, ?, NOW())
    ");

    $result = $stmt->execute([
        $_POST['title'],
        $_POST['subtitle'],
        $_POST['youtube_url'],
        $_POST['youtube_id'],
        isset($_POST['display_order']) ?$_POST['display_order']: 0
    ]);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Video added successfully'
        ]);
    } else {
        throw new Exception('Failed to add video');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}