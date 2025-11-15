<?php
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['success' => false, 'message' => 'Method not allowed']));
}

try {
    if (!isset($_POST['id']) || !isset($_POST['status'])) {
        throw new Exception("Subject ID and status are required");
    }

    $stmt = $pdo->prepare("UPDATE subjects SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $_POST['id']]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}