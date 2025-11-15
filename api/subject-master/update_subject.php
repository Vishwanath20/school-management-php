<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    // Validate required fields
    if (empty($_POST['id'])) throw new Exception('Feature ID is required');
    if (empty($_POST['name'])) throw new Exception('Subject Name Ss Required');

    // Update subject
    $stmt = $pdo->prepare("
        UPDATE subjects SET 
            name = ?, status=? WHERE id = ?
    ");

    $result = $stmt->execute([
        $_POST['name'],
        isset($_POST['status']) ? $_POST['status'] : 1,
        $_POST['id']
    ]);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Failed to update Subject');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}