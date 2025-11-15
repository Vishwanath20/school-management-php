<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    // Validate required fields
    if (empty($_POST['title'])) {
        throw new Exception('Title is required');
    }
    if (empty($_POST['description'])) {
        throw new Exception('Description is required');
    }
    if (empty($_POST['date'])) {
        throw new Exception('Date is required');
    }

    // Prepare the insert query
    $stmt = $pdo->prepare("
        INSERT INTO notices (
            title, 
            description, 
            date, 
            link, 
            link_text, 
            sections,
            badge,
            created_at
        ) VALUES (?, ?, ?, ?, ?,?, ?, NOW())
    ");

    // Execute with parameters
    $result = $stmt->execute([
        $_POST['title'],
        $_POST['description'],
        $_POST['date'],
isset($_POST['link']) ? $_POST['link'] : null,
        isset($_POST['link_text']) ? $_POST['link_text'] : 'Read More',
        isset($_POST['sections']) ? $_POST['sections'] : 0,
isset($_POST['badge']) ? $_POST['badge'] : null
    ]);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Notice added successfully'
        ]);
    } else {
        throw new Exception('Failed to add notice');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}