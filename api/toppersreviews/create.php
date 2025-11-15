<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    // Validate required fields
    if (empty($_POST['name'])) throw new Exception('Name is required');
    if (empty($_POST['exam_rank'])) throw new Exception('Rank is required');
    if (empty($_POST['exam'])) throw new Exception('Exam is required');
    if (empty($_POST['year'])) throw new Exception('Year is required');
    if (empty($_POST['review'])) throw new Exception('Review is required');
    if (empty($_FILES['photo'])) throw new Exception('Photo is required');

    // Handle photo upload
    $photo = $_FILES['photo'];
    $fileName = time() . '_' . $photo['name'];
    $targetPath = '../../uploads/toppers/' . $fileName;

    // Validate image
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($photo['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG, PNG and GIF are allowed');
    }

    if (!move_uploaded_file($photo['tmp_name'], $targetPath)) {
        throw new Exception('Failed to upload photo');
    }

    // Insert review
    $stmt = $pdo->prepare("
        INSERT INTO toppers_reviews (
            name, exam_rank, exam, year, photo, review,
            display_order, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $displayOrder = isset($_POST['display_order']) ? $_POST['display_order'] : 0; // Assign to a variable

    $result = $stmt->execute([
        $_POST['name'],
        $_POST['exam_rank'],
        $_POST['exam'],
        $_POST['year'],
        $fileName,
        $_POST['review'],
        $displayOrder // Use the variable here
    ]);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Review added successfully'
        ]);
    } else {
        unlink($targetPath); // Remove uploaded file if DB insert fails
        throw new Exception('Failed to add review');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}