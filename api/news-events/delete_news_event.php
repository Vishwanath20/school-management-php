<?php
require_once('../../config/database.php');

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;

    if (empty($id)) {
        $response['message'] = 'ID is required.';
        echo json_encode($response);
        exit();
    }

    try {
        // First, get the photo name to delete the file
        $stmt = $pdo->prepare("SELECT photo, youtube_url FROM news_events WHERE id = ?");
        $stmt->execute([$id]);
        $newsEvent = $stmt->fetch();

        if ($newsEvent && !empty($newsEvent['photo'])) {
            $upload_dir = '../../uploads/news-events/';
            $photo_path = $upload_dir . $newsEvent['photo'];
            if (file_exists($photo_path)) {
                unlink($photo_path);
            }
        }

        // Then delete the record from the database
        $stmt = $pdo->prepare("DELETE FROM news_events WHERE id = ?");
        $stmt->execute([$id]);

        $response['success'] = true;
        $response['message'] = 'News/Event deleted successfully.';
    } catch (PDOException $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
