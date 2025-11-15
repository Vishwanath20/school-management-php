<?php
require_once '../../config/database.php';

header('Content-Type: application/json');

try {
    $id = isset($_POST['id']) ? $_POST['id'] : 0;

    if (empty($id)) {
        throw new Exception("Category ID is required");
    }

    // Start transaction
    $pdo->beginTransaction();

    // Delete all photos in this category
    $stmt = $pdo->prepare("SELECT image_path FROM gallery_photos WHERE category_id = ?");
    $stmt->execute([$id]);
    $photos = $stmt->fetchAll();

    // Delete physical files
    foreach ($photos as $photo) {
        $imagePath = "../../../uploads/gallery/" . $photo['image_path'];
        $thumbnailPath = "../../../uploads/gallery/thumbnails/" . $photo['image_path'];
        
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        if (file_exists($thumbnailPath)) {
            unlink($thumbnailPath);
        }
    }

    // Delete photos from database
    $stmt = $pdo->prepare("DELETE FROM gallery_photos WHERE category_id = ?");
    $stmt->execute([$id]);

    // Delete category
    $stmt = $pdo->prepare("DELETE FROM gallery_categories WHERE id = ?");
    $stmt->execute([$id]);

    $pdo->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}