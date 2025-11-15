<?php
require_once('../../config/database.php');

header('Content-Type: application/json');

try {
    // Validate input
    if (empty($_POST['course_id']) || !isset($_POST['amounts']) || !isset($_POST['due_days'])) {
        throw new Exception('Missing required fields');
    }

    $course_id = $_POST['course_id'];
    $amounts = $_POST['amounts'];
    $due_days = $_POST['due_days'];

    // Validate course exists and get price
    $stmt = $pdo->prepare("SELECT price FROM courses WHERE id = ? AND status = 1");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch();
    
    if (!$course) {
        throw new Exception('Invalid course');
    }

    // Validate total amount matches course price
    $total_amount = array_sum($amounts);
    if ($total_amount != $course['price']) {
        throw new Exception('Total installment amount must match course price');
    }

    // Start transaction
    $pdo->beginTransaction();

    // Delete existing installments
    $stmt = $pdo->prepare("DELETE FROM course_installments WHERE course_id = ?");
    $stmt->execute([$course_id]);

    // Insert new installments
    $stmt = $pdo->prepare("INSERT INTO course_installments (course_id, installment_number, amount, due_days) 
                          VALUES (?, ?, ?, ?)");

    foreach ($amounts as $index => $amount) {
        if (!is_numeric($amount) || $amount <= 0) {
            throw new Exception('Invalid amount for installment ' . ($index + 1));
        }

        if (!is_numeric($due_days[$index]) || $due_days[$index] < 0) {
            throw new Exception('Invalid due days for installment ' . ($index + 1));
        }

        $stmt->execute([
            $course_id,
            $index + 1,
            $amount,
            $due_days[$index]
        ]);
    }

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Installment settings saved successfully'
    ]);

} catch (Exception $e) {
    // Rollback transaction if active
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}