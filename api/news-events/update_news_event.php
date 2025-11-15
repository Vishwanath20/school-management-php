<?php
require_once('../../config/database.php');

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $event_name = $_POST['event_name'] ?? '';
    $event_date = $_POST['event_date'] ?? '';
    $status = $_POST['status'] ?? 1;
    $media_type = $_POST['media_type'] ?? 'none';
    $youtube_url = $_POST['youtube_url'] ?? null;
    $current_photo = $_POST['current_photo'] ?? '';

    if (empty($id) || empty($event_name) || empty($event_date)) {
        $response['message'] = 'ID, event name, and date are required.';
        echo json_encode($response);
        exit();
    }

    $photo_name = null; // Will be set if a new photo is uploaded or existing photo is kept
    $new_youtube_url = null; // Will be set if a new YouTube URL is provided

    $upload_dir = '../../uploads/news-events/';

    // Fetch current media to handle deletion if media type changes
    $stmt_fetch = $pdo->prepare("SELECT photo, youtube_url FROM news_events WHERE id = ?");
    $stmt_fetch->execute([$id]);
    $current_media = $stmt_fetch->fetch();

    // Handle photo upload
    if ($media_type === 'photo') {
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $photo_tmp_name = $_FILES['photo']['tmp_name'];
            $photo_name = uniqid() . '_' . basename($_FILES['photo']['name']);
            $photo_path = $upload_dir . $photo_name;

            // Delete old photo if a new one is uploaded
            if (!empty($current_media['photo']) && file_exists($upload_dir . $current_media['photo'])) {
                unlink($upload_dir . $current_media['photo']);
            }
            // If media type changed from youtube to photo, clear youtube_url
            $new_youtube_url = null;

            if (!move_uploaded_file($photo_tmp_name, $photo_path)) {
                $response['message'] = 'Failed to upload new photo.';
                echo json_encode($response);
                exit();
            }
        } else {
            // If no new photo uploaded, keep current photo if it exists and media type is still photo
            $photo_name = $current_photo;
            // If media type changed from youtube to photo, clear youtube_url
            $new_youtube_url = null;
        }
    } elseif ($media_type === 'youtube') {
        $new_youtube_url = $youtube_url;
        // If media type changed from photo to youtube, delete old photo
        if (!empty($current_media['photo']) && file_exists($upload_dir . $current_media['photo'])) {
            unlink($upload_dir . $current_media['photo']);
        }
        $photo_name = null; // Clear photo name
    } else { // media_type is 'none'
        // Delete existing photo if any
        if (!empty($current_media['photo']) && file_exists($upload_dir . $current_media['photo'])) {
            unlink($upload_dir . $current_media['photo']);
        }
        $photo_name = null;
        $new_youtube_url = null;
    }

    try {
        $stmt = $pdo->prepare("UPDATE news_events SET photo = ?, youtube_url = ?, event_name = ?, event_date = ?, status = ? WHERE id = ?");
        $stmt->execute([$photo_name, $new_youtube_url, $event_name, $event_date, $status, $id]);

        $response['success'] = true;
        $response['message'] = 'News/Event updated successfully.';
    } catch (PDOException $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
