<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    if (empty($_POST['id'])) throw new Exception('Resource ID is required');
    
    $stmt = $pdo->prepare("UPDATE course_resources SET status = ? WHERE id = ?");
    $result = $stmt->execute([
        $_POST['status'],
        $_POST['id']
    ]);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update status');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}