<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    // Validate required fields
    if (empty($_POST['title'])) throw new Exception('Title is required');
    if (empty($_POST['description'])) throw new Exception('Description is required');
    if (empty($_FILES['icon'])) throw new Exception('Icon is required');

    // Handle icon upload
    $icon = $_FILES['icon'];
    $fileName = time() . '_' . $icon['name'];
    $targetPath = '../../uploads/features/' . $fileName;

    // Validate image
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($icon['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG, PNG and GIF are allowed');
    }

    if (!move_uploaded_file($icon['tmp_name'], $targetPath)) {
        throw new Exception('Failed to upload icon');
    }

    // Insert feature
    $stmt = $pdo->prepare("
        INSERT INTO why_choose_us (
            title, description, icon, display_order, created_at
        ) VALUES (?, ?, ?, ?, NOW())
    ");

    $result = $stmt->execute([
        $_POST['title'],
        $_POST['description'],
        $fileName,
        isset($_POST['display_order']) ? $_POST['display_order'] : 0
    ]);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Feature added successfully'
        ]);
    } else {
        unlink($targetPath); // Remove uploaded file if DB insert fails
        throw new Exception('Failed to add feature');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}