<?php
require_once '../../config/database.php';

header('Content-Type: application/json');

try {
    $id = isset($_POST['id']) ?$_POST['id']: 0;
    $image_path = isset($_POST['image_path']) ?$_POST['image_path']: '';

    if (empty($id)) {
        throw new Exception("Photo ID is required");
    }

    // Delete physical files
    $originalPath = "../../uploads/gallery/" . $image_path;
    $thumbnailPath = "../../uploads/gallery/thumbnails/" . $image_path;
    
    if (file_exists($originalPath)) {
        unlink($originalPath);
    }
    if (file_exists($thumbnailPath)) {
        unlink($thumbnailPath);
    }

    // Delete from database
    $stmt = $pdo->prepare("DELETE FROM gallery_photos WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}