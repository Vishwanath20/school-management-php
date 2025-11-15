<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    if (!isset($_GET['course_id'])) {
        throw new Exception('Course ID is required');
    }

    $stmt = $pdo->prepare("SELECT id, name FROM batches WHERE course_id = ? AND status = 1 ORDER BY name");
    $stmt->execute([$_GET['course_id']]);
    $batches = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'batches' => $batches
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}