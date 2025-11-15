<?php
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['success' => false, 'message' => 'Method not allowed']));
}

try {
    if (empty($_POST['name'])) {
        throw new Exception("Subject name is required");
    }

    // Check if subject already exists
    $stmt = $pdo->prepare("SELECT id FROM subjects WHERE name = ?");
    $stmt->execute([$_POST['name']]);
    if ($stmt->fetch()) {
        throw new Exception("Subject already exists");
    }

    $stmt = $pdo->prepare("INSERT INTO subjects (name, status) VALUES (?, ?)");
    $stmt->execute([
        $_POST['name'],
        isset($_POST['status']) ? $_POST['status'] : 1
    ]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}