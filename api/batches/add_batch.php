<?php
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['success' => false, 'message' => 'Method not allowed']));
}

try {
    // Validate inputs
    $required_fields = ['name', 'course_id', 'faculty_id', 'start_date', 'end_date', 'timing', 'capacity'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("$field is required");
        }
    }

    // Validate dates
    if (strtotime($_POST['end_date']) <= strtotime($_POST['start_date'])) {
        throw new Exception("End date must be after start date");
    }

    $stmt = $pdo->prepare("
        INSERT INTO batches (name, course_id, faculty_id, start_date, end_date, timing, capacity, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $_POST['name'],
        $_POST['course_id'],
        $_POST['faculty_id'],
        $_POST['start_date'],
        $_POST['end_date'],
        $_POST['timing'],
        $_POST['capacity'],
        isset($_POST['status']) ? $_POST['status'] : 1
    ]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}