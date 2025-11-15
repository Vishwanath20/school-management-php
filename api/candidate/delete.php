<?php
header('Content-Type: application/json');
require_once('../../config/database.php');

try {
    // Validate input
    if (!isset($_POST['id'])) {
        throw new Exception('User ID is required');
    }

    $id = intval($_POST['id']);

    // Get user details for profile image
    $stmt = $pdo->prepare("SELECT profile_image FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    // Delete user
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $result = $stmt->execute([$id]);

    if ($result) {
        // Delete profile image if exists
        if ($user && $user['profile_image'] && $user['profile_image'] !== 'default.png') {
            $imagePath = '../../uploads/profiles/' . $user['profile_image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'User deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete user');
    }

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}