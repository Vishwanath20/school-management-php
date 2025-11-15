<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    if (empty($_POST['id'])) {
        throw new Exception('Review ID is required');
    }

    // Get review photo before deletion
    $stmt = $pdo->prepare("SELECT photo FROM toppers_reviews WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    $review = $stmt->fetch();

    if ($review) {
        // Delete the review
        $stmt = $pdo->prepare("DELETE FROM toppers_reviews WHERE id = ?");
        $result = $stmt->execute([$_POST['id']]);

        if ($result) {
            // Delete photo file
            if ($review['photo']) {
                $photoPath = '../../uploads/toppers/' . $review['photo'];
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
            }

            echo json_encode([
                'status' => 'success',
                'message' => 'Review deleted successfully'
            ]);
        } else {
            throw new Exception('Failed to delete review');
        }
    } else {
        throw new Exception('Review not found');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}