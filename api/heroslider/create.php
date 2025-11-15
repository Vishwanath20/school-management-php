<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    // Validate required fields
    // if (empty($_POST['title'])) {
    //     throw new Exception('Title is required');
    // }

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
        throw new Exception('Slider image is required');
    }

    // Handle image upload
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_ext, $allowed)) {
        throw new Exception('Invalid file type. Allowed types: ' . implode(', ', $allowed));
    }

    $fileName = 'slider_' . time() . '.' . $file_ext;
    $uploadPath = '../../uploads/sliders/' . $fileName;
    
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
        throw new Exception('Failed to upload image');
    }

    // Insert slider
    $stmt = $pdo->prepare("INSERT INTO hero_sliders (title, subtitle, description, image, button_text,display_order, button_link, created_at) VALUES (?, ?, ?,?, ?, ?, ?, NOW())");
    
    $result = $stmt->execute([
        $_POST['title'],
        isset($_POST['subtitle']) ? $_POST['subtitle'] : '',
        isset($_POST['description']) ? $_POST['description'] : '',
        $fileName,
        isset($_POST['button_text']) ? $_POST['button_text'] : '',
        isset($_POST['display_order']) ? $_POST['display_order'] : '',
        isset($_POST['button_link']) ? $_POST['button_link'] : ''
    ]);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Slider added successfully'
        ]);
    } else {
        throw new Exception('Failed to add slider');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}