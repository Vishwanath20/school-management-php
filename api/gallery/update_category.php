<?php
require_once '../../config/database.php';

header('Content-Type: application/json');

try {
    $id = isset($_POST['id']) ? $_POST['id'] : 0;
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $status = $_POST['status']=='1' ? 1 : 0;

    if (empty($id) || empty($title)) {
        throw new Exception("ID and title are required");
    }

    $stmt = $pdo->prepare("UPDATE gallery_categories SET title = ?, description = ?, status = ? WHERE id = ?");
    $stmt->execute([$title, $description, $status, $id]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}