<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        throw new Exception('Slider ID is required');
    }

    $sliderId = $_POST['id'];

    // Get slider details for image deletion
    $stmt = $pdo->prepare("SELECT image FROM hero_sliders WHERE id = ?");
    $stmt->execute([$sliderId]);
    $slider = $stmt->fetch();

    // Delete slider
    $stmt = $pdo->prepare("DELETE FROM hero_sliders WHERE id = ?");
    $result = $stmt->execute([$sliderId]);

    if ($result) {
        // Delete image file if exists
        if ($slider && $slider['image']) {
            $imagePath = '../../uploads/sliders/' . $slider['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Slider deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete slider');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}