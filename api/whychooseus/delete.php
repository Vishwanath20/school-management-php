<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    if (empty($_POST['id'])) {
        throw new Exception('Feature ID is required');
    }

    // Get feature icon before deletion
    $stmt = $pdo->prepare("SELECT icon FROM why_choose_us WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    $feature = $stmt->fetch();

    if ($feature) {
        // Delete the feature
        $stmt = $pdo->prepare("DELETE FROM why_choose_us WHERE id = ?");
        $result = $stmt->execute([$_POST['id']]);

        if ($result) {
            // Delete icon file
            if ($feature['icon']) {
                $iconPath = '../../uploads/features/' . $feature['icon'];
                if (file_exists($iconPath)) {
                    unlink($iconPath);
                }
            }

            echo json_encode([
                'status' => 'success',
                'message' => 'Feature deleted successfully'
            ]);
        } else {
            throw new Exception('Failed to delete feature');
        }
    } else {
        throw new Exception('Feature not found');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}