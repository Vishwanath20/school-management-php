<?php
require_once '../../config/database.php';

header('Content-Type: application/json');

try {
    $id = isset($_POST['id']) ? $_POST['id'] : 0;
    $category_id = isset($_POST['category_id']) ? $_POST['category_id'] : '';
    $status = isset($_POST['status']) ? $_POST['status'] : 1;
    // Get existing photo data
    $stmt = $pdo->prepare("SELECT image_path FROM gallery_photos WHERE id = ?");
    $stmt->execute([$id]);
    $photo = $stmt->fetch();

    if (!$photo) {
        throw new Exception("Photo not found");
    }

    $fileName = $photo['image_path'];

    // Handle new file upload if provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $fileName = time() . '_' . basename($file['name']);
        $uploadPath = '../../uploads/gallery/' . $fileName;

        // Check file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception("Invalid file type. Only JPG, PNG, and GIF allowed");
        }

        // Upload new image
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new Exception("Failed to upload image");
        }

        // Delete old file
        @unlink('../../uploads/gallery/' . $photo['image_path']);
    }

    // Update database
    $stmt = $pdo->prepare("UPDATE gallery_photos SET category_id = ?, image_path = ?, status = ? WHERE id = ?");
    $stmt->execute([$category_id, $fileName, $status, $id]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
