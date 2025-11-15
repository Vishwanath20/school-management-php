<?php
header('Content-Type: application/json');
require_once('../../config/database.php');

try {
    // Validate input
    if (!isset($_POST['user_id']) || !isset($_POST['month']) || !isset($_POST['basic_salary'])) {
        throw new Exception('Required parameters are missing');
    }

    $userId = intval($_POST['user_id']);
    $month = $_POST['month'];
    $basicSalary = floatval($_POST['basic_salary']);
    $allowances = floatval($_POST['allowances']);
    $deductions = floatval($_POST['deductions']);
    $netSalary = floatval($_POST['net_salary']);
    $paymentStatus = $_POST['payment_status'];
    $paymentDate = $paymentStatus == 'paid' ? $_POST['payment_date'] : null;
    $remarks = $_POST['remarks'];

    // Check if salary record exists
    $stmt = $pdo->prepare("SELECT id FROM salaries WHERE user_id = ? AND month = ?");
    $stmt->execute([$userId, $month]);
    $exists = $stmt->fetch();

    if ($exists) {
        // Update existing salary
        $stmt = $pdo->prepare("
            UPDATE salaries 
            SET basic_salary = ?, allowances = ?, deductions = ?, net_salary = ?,
                payment_status = ?, payment_date = ?, remarks = ?, updated_at = NOW()
            WHERE user_id = ? AND month = ?
        ");
        $result = $stmt->execute([
            $basicSalary, $allowances, $deductions, $netSalary,
            $paymentStatus, $paymentDate, $remarks, $userId, $month
        ]);
    } else {
        // Insert new salary
        $stmt = $pdo->prepare("
            INSERT INTO salaries (
                user_id, month, basic_salary, allowances, deductions, 
                net_salary, payment_status, payment_date, remarks
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $result = $stmt->execute([
            $userId, $month, $basicSalary, $allowances, $deductions,
            $netSalary, $paymentStatus, $paymentDate, $remarks
        ]);
    }

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Salary details saved successfully'
        ]);
    } else {
        throw new Exception('Failed to save salary details');
    }

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}