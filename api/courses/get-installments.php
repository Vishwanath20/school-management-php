<?php
require_once('../../config/database.php');

header('Content-Type: application/json');

try {
    if (empty($_GET['course_id'])) {
        throw new Exception('Course ID is required');
    }

    $stmt = $pdo->prepare("SELECT * FROM course_installments 
                          WHERE course_id = ? AND status = 1 
                          ORDER BY installment_number");
    $stmt->execute([$_GET['course_id']]);
    $installments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'installments' => $installments
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}