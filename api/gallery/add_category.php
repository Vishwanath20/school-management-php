<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
$title = isset($_POST['title']) ? $_POST['title'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $status = isset($_POST['status']) ? 1 : 0;

    if (empty($title)) {
        throw new Exception("Title is required");
    }

    $stmt = $pdo->prepare("INSERT INTO gallery_categories (title, description, status) VALUES (?, ?, ?)");
    $stmt->execute([$title, $description, $status]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}