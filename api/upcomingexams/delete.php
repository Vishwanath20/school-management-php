<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    if (empty($_POST['id'])) {
        throw new Exception('Exam ID is required');
    }

    $stmt = $pdo->prepare("DELETE FROM upcoming_exams WHERE id = ?");
    $result = $stmt->execute([$_POST['id']]);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Exam deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete exam');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}