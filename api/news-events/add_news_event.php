<?php
require_once('../../config/database.php');

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_name = $_POST['event_name'] ?? '';
    $event_date = $_POST['event_date'] ?? '';
    $status = $_POST['status'] ?? 1;
    $media_type = $_POST['media_type'] ?? 'none';
    $youtube_url = $_POST['youtube_url'] ?? null;

    if (empty($event_name) || empty($event_date)) {
        $response['message'] = 'Event name and date are required.';
        echo json_encode($response);
        exit();
    }

    $photo_name = null;

    if ($media_type === 'photo' && isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../uploads/news-events/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $photo_tmp_name = $_FILES['photo']['tmp_name'];
        $photo_name = uniqid() . '_' . basename($_FILES['photo']['name']);
        $photo_path = $upload_dir . $photo_name;

        if (!move_uploaded_file($photo_tmp_name, $photo_path)) {
            $response['message'] = 'Failed to upload photo.';
            echo json_encode($response);
            exit();
        }
    } elseif ($media_type === 'youtube' && !empty($youtube_url)) {
        // Validate YouTube URL if needed, for now just store it
    } elseif ($media_type !== 'none') {
        $response['message'] = 'Please provide either a photo or a YouTube URL based on your selection.';
        echo json_encode($response);
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO news_events (photo, youtube_url, event_name, event_date, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$photo_name, $youtube_url, $event_name, $event_date, $status]);

        $response['success'] = true;
        $response['message'] = 'News/Event added successfully.';
    } catch (PDOException $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
