<?php
require_once('../../config/database.php');
header('Content-Type: application/json');

try {
    // Validate input
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        throw new Exception('Invalid category ID');
    }

    $categoryId = (int)$_POST['id'];

    // Begin transaction
    $pdo->beginTransaction();

    // First, check if the category exists
    $stmt = $pdo->prepare("SELECT id FROM course_categories WHERE id = ?");
    $stmt->execute([$categoryId]);
    if (!$stmt->fetch()) {
        throw new Exception('Category not found');
    }

    // Delete the category
    $stmt = $pdo->prepare("DELETE FROM course_categories WHERE id = ?");
    $stmt->execute([$categoryId]);

    // Commit transaction
    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}