<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    $id = isset($_POST['id']) ? $_POST['id'] : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : 0;

    if (empty($id)) {
        throw new Exception("Category ID is required");
    }

    $stmt = $pdo->prepare("UPDATE gallery_categories SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    echo json_encode([
        'success' => true,
        'message' => 'Status updated successfully'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}