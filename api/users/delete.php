<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    // Check if ID is provided
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        throw new Exception('User ID is required');
    }

    $userId = $_POST['id'];

    // Get user details for profile pic deletion
    $stmt = $pdo->prepare("SELECT profile_pic FROM admin_users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    // Delete user
    $stmt = $pdo->prepare("DELETE FROM admin_users WHERE id = ?");
    $result = $stmt->execute([$userId]);

    if ($result) {
        // Delete profile picture if exists
        if ($user && $user['profile_pic']) {
            $profilePicPath = '../../uploads/profile_pics/' . $user['profile_pic'];
            if (file_exists($profilePicPath)) {
                unlink($profilePicPath);
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
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}