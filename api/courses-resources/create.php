<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    // Validate required fields
    if (empty($_POST['course_id'])) throw new Exception('Course is required');
    if (empty($_POST['title'])) throw new Exception('Title is required');
    if (empty($_POST['new_items'])) throw new Exception('At least one resource item is required');

    $pdo->beginTransaction();

    // Insert main resource
    $stmt = $pdo->prepare("
        INSERT INTO course_resources (course_id, title, description, status, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");

    $stmt->execute([
        $_POST['course_id'],
        $_POST['title'],
        $_POST['description'] ?? '',
        $_POST['status'] ?? 1
    ]);

    $resourceId = $pdo->lastInsertId();

    // Process resource items
    foreach ($_POST['new_items'] as $index => $item) {
        if (empty($item['type'])) continue;

        $content = '';
        if ($item['type'] === 'video') {
            $content = $item['content'];
        } else {
            // Handle file upload
            if (!isset($_FILES['new_items']['name'][$index]['content'])) continue;

            $file = [
                'name' => $_FILES['new_items']['name'][$index]['content'],
                'type' => $_FILES['new_items']['type'][$index]['content'],
                'tmp_name' => $_FILES['new_items']['tmp_name'][$index]['content'],
                'error' => $_FILES['new_items']['error'][$index]['content'],
                'size' => $_FILES['new_items']['size'][$index]['content']
            ];

            if ($file['error'] !== 0) continue;

            $fileName = time() . '_' . $file['name'];
            $uploadPath = '../../uploads/resources/' . $fileName;

            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                throw new Exception('Failed to upload file: ' . $file['name']);
            }

            $content = $fileName;
        }

        $stmt = $pdo->prepare("
            INSERT INTO resource_items (resource_id, type, content, sort_order)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([$resourceId, $item['type'], $content, $index]);
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Resource added successfully'
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}