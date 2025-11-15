<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    if (empty($_POST['id'])) {
        throw new Exception('Faculty ID is required');
    }

    // Get faculty photo before deletion
    $stmt = $pdo->prepare("SELECT photo FROM faculty WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    $faculty = $stmt->fetch();

    if ($faculty) {
        // Delete the faculty
        $stmt = $pdo->prepare("DELETE FROM faculty WHERE id = ?");
        $result = $stmt->execute([$_POST['id']]);

        if ($result) {
            // Delete photo file
            if ($faculty['photo']) {
                $photoPath = '../../uploads/faculty/' . $faculty['photo'];
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
            }

            echo json_encode([
                'status' => 'success',
                'message' => 'Faculty deleted successfully'
            ]);
        } else {
            throw new Exception('Failed to delete faculty');
        }
    } else {
        throw new Exception('Faculty not found');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}