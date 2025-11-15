<?php
header('Content-Type: application/json');
require_once('../../config/database.php');

try {
    // Validate input
    if (!isset($_POST['lead_id']) || !isset($_POST['staff_id'])) {
        throw new Exception('Required parameters are missing');
    }

    $lead_id = intval($_POST['lead_id']);
    $staff_id = $_POST['staff_id'] ? intval($_POST['staff_id']) : null;
    $remarks = isset($_POST['remarks']) ? $_POST['remarks'] : null;

    // Update the lead assignment and remarks
    $stmt = $pdo->prepare("UPDATE contact_enquiries SET assigned_with = ?, remarks = ?, updated_at = NOW() WHERE id = ?");
    $result = $stmt->execute([$staff_id, $remarks, $lead_id]);

    if ($result) {
        echo json_encode(['status' => 'success']);
    } else {
        throw new Exception('Failed to update lead assignment');
    }

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}