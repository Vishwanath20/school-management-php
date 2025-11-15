<?php
require_once '../../config/database.php';

header('Content-Type: application/json');

try {
    // Validate input
    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['phone']) || empty($_POST['course_interest'])) {
        throw new Exception('Please fill all required fields');
    }

    // Validate email
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Prepare and execute the query
    $stmt = $pdo->prepare("INSERT INTO contact_enquiries (name, email, phone,source, course_interest, message) VALUES (?, ?, ?, ?, ?, ?)");
    
    $result = $stmt->execute([
        $_POST['name'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['source'],
        $_POST['course_interest'],
isset($_POST['message']) ? $_POST['message'] : ''
    ]);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Failed to save enquiry');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}