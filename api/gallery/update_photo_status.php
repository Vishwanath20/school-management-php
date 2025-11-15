<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    $id = isset($_POST['id']) ?$_POST['id']: 0;
    $status = isset($_POST['status']) ? $_POST['status']: 0;

    if (empty($id)) {
        throw new Exception("Photo ID is required");
    }

    $stmt = $pdo->prepare("UPDATE gallery_photos SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}