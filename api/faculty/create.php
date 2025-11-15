<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    // Validate required fields
    if (empty($_POST['name'])) throw new Exception('Name is required');
    if (empty($_POST['title'])) throw new Exception('Title is required');
    if (empty($_POST['specialization'])) throw new Exception('Specialization is required');
    if (empty($_POST['experience'])) throw new Exception('Experience is required');
    if (empty($_FILES['photo'])) throw new Exception('Photo is required');

    // Handle photo upload
    $photo = $_FILES['photo'];
    $fileName = time() . '_' . $photo['name'];
    $targetPath = '../../uploads/faculty/' . $fileName;

    // Validate image
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($photo['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG, PNG and GIF are allowed');
    }

    if (!move_uploaded_file($photo['tmp_name'], $targetPath)) {
        throw new Exception('Failed to upload photo');
    }

    // Insert faculty
    $stmt = $pdo->prepare("
        INSERT INTO faculty (
            title, name, specialization, experience, photo,
            linkedin, twitter, display_order, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $result = $stmt->execute([
        $_POST['title'],
        $_POST['name'],
        $_POST['specialization'],
        $_POST['experience'],
        $fileName,
        isset($_POST['linkedin']) ? $_POST['linkedin']: null,
        isset($_POST['twitter']) ? $_POST['twitter']: null,
        isset($_POST['display_order']) ? $_POST['display_order']: 0
    ]);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Faculty added successfully'
        ]);
    } else {
        unlink($targetPath); // Remove uploaded file if DB insert fails
        throw new Exception('Failed to add faculty');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}