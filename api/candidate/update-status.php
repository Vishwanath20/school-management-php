<?php
header('Content-Type: application/json');
require_once('../../config/database.php');

try {
    // Validate input
    if (!isset($_POST['id']) || !isset($_POST['status'])) {
        throw new Exception('Required parameters are missing');
    }

    $id = intval($_POST['id']);
    $status = intval($_POST['status']);

    // Update user status
    $stmt = $pdo->prepare("UPDATE users SET status = ?, updated_at = NOW() WHERE id = ?");
    $result = $stmt->execute([$status, $id]);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'User status updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update user status');
    }

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}