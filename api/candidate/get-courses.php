<?php
header('Content-Type: application/json');
require_once('../../config/database.php');

try {
    if (!isset($_GET['user_id'])) {
        throw new Exception('User ID is required');
    }

    $userId = intval($_GET['user_id']);

    // Fetch user's courses with course details
    $stmt = $pdo->prepare("
        SELECT 
            c.title,
            c.type,
            c.price,
            DATE_FORMAT(uc.created_at, '%d %b %Y') as purchase_date
        FROM user_courses uc
        JOIN courses c ON uc.course_id = c.id
        WHERE uc.user_id = ?
        ORDER BY uc.created_at DESC
    ");
    
    $stmt->execute([$userId]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'courses' => $courses
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}