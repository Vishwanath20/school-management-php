<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    // Validate required fields
    if (empty($_POST['id'])) throw new Exception('Review ID is required');
    if (empty($_POST['name'])) throw new Exception('Name is required');
    if (empty($_POST['exam_rank'])) throw new Exception('Rank is required');
    if (empty($_POST['exam'])) throw new Exception('Exam is required');
    if (empty($_POST['year'])) throw new Exception('Year is required');
    if (empty($_POST['review'])) throw new Exception('Review is required');

    // Get current review data
    $stmt = $pdo->prepare("SELECT photo FROM toppers_reviews WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    $currentReview = $stmt->fetch();

    $fileName = $currentReview['photo'];

    // Handle new photo upload if provided
    if (!empty($_FILES['photo']['name'])) {
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

        // Delete old photo
        if ($currentReview['photo']) {
            $oldFile = '../../uploads/toppers/' . $currentReview['photo'];
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
    }

    // Update review
    $stmt = $pdo->prepare("
        UPDATE toppers_reviews SET 
            name = ?,
            exam_rank = ?,
            exam = ?,
            year = ?,
            photo = ?,
            review = ?,
            display_order = ?,
            updated_at = NOW()
        WHERE id = ?
    ");

    $result = $stmt->execute([
        $_POST['name'],
        $_POST['exam_rank'],
        $_POST['exam'],
        $_POST['year'],
        $fileName,
        $_POST['review'],
        isset($_POST['display_order']) ? $_POST['display_order'] : 0,
        $_POST['id']
    ]);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Review updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update review');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}