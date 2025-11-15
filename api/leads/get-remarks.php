<?php
header('Content-Type: application/json');
require_once('../../config/database.php');

try {
    if (!isset($_GET['lead_id'])) {
        throw new Exception('Lead ID is required');
    }

    $lead_id = intval($_GET['lead_id']);
    
    $stmt = $pdo->prepare("SELECT remarks FROM contact_enquiries WHERE id = ?");
    $stmt->execute([$lead_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'remarks' => isset($result['remarks']) ? $result['remarks'] : null
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}