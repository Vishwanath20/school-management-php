<?php
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['success' => false, 'message' => 'Method not allowed']));
}

try {
    if (empty($_POST['id'])) {
        throw new Exception("Subject ID is required");
    }

    // Check if subject is being used in batch content
    $stmt = $pdo->prepare("SELECT id FROM batch_content WHERE subject_id = ? LIMIT 1");
    $stmt->execute([$_POST['id']]);
    if ($stmt->fetch()) {
        throw new Exception("Cannot delete subject as it is being used in batch content");
    }

    $stmt = $pdo->prepare("DELETE FROM subjects WHERE id = ?");
    $stmt->execute([$_POST['id']]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}