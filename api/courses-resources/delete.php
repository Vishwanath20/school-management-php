<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    if (empty($_POST['id'])) throw new Exception('Resource ID is required');

    $pdo->beginTransaction();

    // Get all resource items to delete their files
    $stmt = $pdo->prepare("SELECT type, content FROM resource_items WHERE resource_id = ?");
    $stmt->execute([$_POST['id']]);
    $items = $stmt->fetchAll();

    // Delete files
    foreach ($items as $item) {
        if ($item['type'] !== 'video') {
            $filePath = '../../uploads/resources/' . $item['content'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }

    // Delete resource items
    $stmt = $pdo->prepare("DELETE FROM resource_items WHERE resource_id = ?");
    $stmt->execute([$_POST['id']]);

    // Delete main resource
    $stmt = $pdo->prepare("DELETE FROM course_resources WHERE id = ?");
    $result = $stmt->execute([$_POST['id']]);

    if ($result) {
        $pdo->commit();
        echo json_encode([
            'success' => true,
            'message' => 'Resource deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete resource');
    }

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