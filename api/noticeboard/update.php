<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    // Validate required fields
    if (!isset($_POST['id'])) {
        throw new Exception('Notice ID is required');
    }
    if (empty($_POST['title'])) {
        throw new Exception('Title is required');
    }
    if (empty($_POST['description'])) {
        throw new Exception('Description is required');
    }
    if (empty($_POST['date'])) {
        throw new Exception('Date is required');
    }

    // Prepare the update query
    $stmt = $pdo->prepare("
        UPDATE notices SET 
            title = ?,
            description = ?,
            date = ?,
            link = ?,
            link_text = ?,
             sections = ?,
            badge = ?,
            updated_at = NOW()
        WHERE id = ?
    ");

    // Execute with parameters
    $result = $stmt->execute([
        $_POST['title'],
        $_POST['description'],
        $_POST['date'],
isset($_POST['link']) ? $_POST['link'] : null,
isset($_POST['link_text']) ? $_POST['link_text'] : 'Read More',
        isset($_POST['sections'])? $_POST['sections'] : 0,
        isset($_POST['badge']) ? $_POST['badge'] : null,
        $_POST['id']
    ]);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Notice updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update notice');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}