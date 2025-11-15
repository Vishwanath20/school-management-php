<?php
require_once '../../config/database.php';

header('Content-Type: application/json');

try {

    $category_id = isset($_POST['category_id']) ? $_POST['category_id'] : '';
    $status = isset($_POST['status']) ? $_POST['status'] : 1;

    // Handle multiple file uploads
    if (!isset($_FILES['images']) || empty($_FILES['images']['name'][0])) {
        throw new Exception("Please select at least one image");
    }

    $uploadedFiles = [];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif','image/webp'];

    // Create directory if not exists
    if (!file_exists('../../uploads/gallery')) {
        mkdir('../../uploads/gallery', 0777, true);
    }

    // Start transaction
    $pdo->beginTransaction();

    // Process each uploaded file
    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
        $file_type = $_FILES['images']['type'][$key];
        if (!in_array($file_type, $allowedTypes)) {
            throw new Exception("Invalid file type. Only JPG, PNG and GIF allowed");
        }

        $fileName = time() . '_' . $key . '_' . basename($_FILES['images']['name'][$key]);
        $uploadPath = '../../uploads/gallery/' . $fileName;

        // Upload original image
        if (!move_uploaded_file($tmp_name, $uploadPath)) {
            throw new Exception("Failed to upload image: " . $_FILES['images']['name'][$key]);
        }

        // Save to database
        $stmt = $pdo->prepare("INSERT INTO gallery_photos (category_id, image_path, status) VALUES (?, ?, ?)");
        $stmt->execute([$category_id, $fileName, $status]);

        $uploadedFiles[] = $fileName;
    }

    $pdo->commit();
    echo json_encode([
        'success' => true,
        'message' => count($uploadedFiles) . ' photos uploaded successfully'
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Delete any uploaded files if there was an error
    foreach ($uploadedFiles as $file) {
        @unlink('../../uploads/gallery/' . $file);
    }
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
