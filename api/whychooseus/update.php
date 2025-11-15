<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    // Validate required fields
    if (empty($_POST['id'])) throw new Exception('Feature ID is required');
    if (empty($_POST['title'])) throw new Exception('Title is required');
    if (empty($_POST['description'])) throw new Exception('Description is required');

    // Get current feature data
    $stmt = $pdo->prepare("SELECT icon FROM why_choose_us WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    $currentFeature = $stmt->fetch();

    $fileName = $currentFeature['icon'];

    // Handle new icon upload if provided
    if (!empty($_FILES['icon']['name'])) {
        $icon = $_FILES['icon'];
        $fileName = time() . '_' . $icon['name'];
        $targetPath = '../../uploads/features/' . $fileName;

        // Validate image
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($icon['type'], $allowedTypes)) {
            throw new Exception('Invalid file type. Only JPG, PNG and GIF are allowed');
        }

        if (!move_uploaded_file($icon['tmp_name'], $targetPath)) {
            throw new Exception('Failed to upload icon');
        }

        // Delete old icon
        if ($currentFeature['icon']) {
            $oldFile = '../../uploads/features/' . $currentFeature['icon'];
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
    }

    // Update feature
    $stmt = $pdo->prepare("
        UPDATE why_choose_us SET 
            title = ?,
            description = ?,
            icon = ?,
            display_order = ?,
            updated_at = NOW()
        WHERE id = ?
    ");

    $result = $stmt->execute([
        $_POST['title'],
        $_POST['description'],
        $fileName,
        isset($_POST['display_order']) ? $_POST['display_order'] : 0,
        $_POST['id']
    ]);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Feature updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update feature');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}