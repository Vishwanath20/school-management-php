<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    // Validate required fields
    if (empty($_POST['id'])) throw new Exception('Exam ID is required');
    if (empty($_POST['title'])) throw new Exception('Title is required');
    if (empty($_POST['exam_date'])) throw new Exception('Exam date is required');
    if (empty($_POST['background_color'])) throw new Exception('Background color is required');

    // Update exam
    $stmt = $pdo->prepare("
        UPDATE upcoming_exams SET 
            title = ?,
            exam_date = ?,
            background_color = ?,
            display_order = ?,
            updated_at = NOW()
        WHERE id = ?
    ");

    $result = $stmt->execute([
        $_POST['title'],
        $_POST['exam_date'],
        $_POST['background_color'],
        isset($_POST['display_order']) ?$_POST['display_order']: 0,
        $_POST['id']
    ]);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Exam updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update exam');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}