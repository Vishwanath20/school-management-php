<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    if (empty($_POST['id'])) throw new Exception('Resource ID is required');
    if (empty($_POST['course_id'])) throw new Exception('Course is required');
    if (empty($_POST['title'])) throw new Exception('Title is required');

    $pdo->beginTransaction();

    // Update main resource
    $stmt = $pdo->prepare("
        UPDATE course_resources 
        SET course_id = ?, title = ?, description = ?, status = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $_POST['course_id'],
        $_POST['title'],
        $_POST['description'] ?? '',
        $_POST['status'] ?? 1,
        $_POST['id']
    ]);

    // Update existing items
    if (!empty($_POST['items'])) {
        foreach ($_POST['items'] as $itemId => $item) {
            if ($item['type'] === 'video') {
                // Update video URL
                $stmt = $pdo->prepare("
                    UPDATE resource_items 
                    SET type = ?, content = ?, sort_order = ?
                    WHERE id = ? AND resource_id = ?
                ");
                $stmt->execute([
                    $item['type'],
                    $item['content'],
                    $item['sort_order'] ?? 0,
                    $itemId,
                    $_POST['id']
                ]);
            } else if (isset($_FILES['items']['name'][$itemId]['content'])) {
                // Handle new file upload
                $file = [
                    'name' => $_FILES['items']['name'][$itemId]['content'],
                    'type' => $_FILES['items']['type'][$itemId]['content'],
                    'tmp_name' => $_FILES['items']['tmp_name'][$itemId]['content'],
                    'error' => $_FILES['items']['error'][$itemId]['content'],
                    'size' => $_FILES['items']['size'][$itemId]['content']
                ];

                if ($file['error'] === 0) {
                    // Get current file to delete
                    $stmt = $pdo->prepare("SELECT content FROM resource_items WHERE id = ?");
                    $stmt->execute([$itemId]);
                    $oldFile = $stmt->fetchColumn();

                    // Upload new file
                    $fileName = time() . '_' . $file['name'];
                    $uploadPath = '../../uploads/resources/' . $fileName;

                    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                        throw new Exception('Failed to upload file: ' . $file['name']);
                    }

                    // Update database
                    $stmt = $pdo->prepare("
                        UPDATE resource_items 
                        SET type = ?, content = ?, sort_order = ?
                        WHERE id = ? AND resource_id = ?
                    ");
                    $stmt->execute([
                        $item['type'],
                        $fileName,
                        $item['sort_order'] ?? 0,
                        $itemId,
                        $_POST['id']
                    ]);

                    // Delete old file
                    if ($oldFile) {
                        $oldPath = '../../uploads/resources/' . $oldFile;
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }
                }
            }
        }
    }

    // Add new items
    if (!empty($_POST['new_items'])) {
        foreach ($_POST['new_items'] as $index => $item) {
            if (empty($item['type'])) continue;

            $content = '';
            if ($item['type'] === 'video' || $item['type'] === 'googleDrive') {
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

            $stmt->execute([$_POST['id'], $item['type'], $content, $index]);
        }
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Resource updated successfully'
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