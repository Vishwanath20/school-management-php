<?php
header('Content-Type: application/json');
require_once('../../config/database.php');

try {
    // Validate input
    if (!isset($_POST['user_id']) || !isset($_POST['date']) || !isset($_POST['status'])) {
        throw new Exception('Required parameters are missing');
    }

    $userId = intval($_POST['user_id']);
    $date = $_POST['date'];
    $status = $_POST['status'];
    $inTime = $_POST['in_time'] ?: null;
    $outTime = $_POST['out_time'] ?: null;
    $remarks = $_POST['remarks'] ?: null;

    // Check if attendance already exists
    $stmt = $pdo->prepare("SELECT id FROM attendance WHERE user_id = ? AND date = ?");
    $stmt->execute([$userId, $date]);
    $exists = $stmt->fetch();

    if ($exists) {
        // Update existing attendance
        $stmt = $pdo->prepare("
            UPDATE attendance 
            SET status = ?, in_time = ?, out_time = ?, remarks = ?, updated_at = NOW()
            WHERE user_id = ? AND date = ?
        ");
        $result = $stmt->execute([$status, $inTime, $outTime, $remarks, $userId, $date]);
    } else {
        // Insert new attendance
        $stmt = $pdo->prepare("
            INSERT INTO attendance (user_id, date, status, in_time, out_time, remarks, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $result = $stmt->execute([$userId, $date, $status, $inTime, $outTime, $remarks]);
    }

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Attendance saved successfully'
        ]);
    } else {
        throw new Exception('Failed to save attendance');
    }

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}