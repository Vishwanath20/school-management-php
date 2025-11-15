<?php
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['success' => false, 'message' => 'Method not allowed']));
}

try {
    if (empty($_POST['id'])) {
        throw new Exception("Batch ID is required");
    }

    $stmt = $pdo->prepare("DELETE FROM batches WHERE id = ?");
    $stmt->execute([$_POST['id']]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}