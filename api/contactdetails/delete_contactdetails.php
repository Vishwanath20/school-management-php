<?php
require_once('../../config/database.php');
header('Content-Type: application/json');

try {
    // Validate input
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        throw new Exception('Invalid contact ID');
    }

    $contactId = (int)$_POST['id'];

    // Begin transaction
    $pdo->beginTransaction();

    // First, check if the category exists
    $stmt = $pdo->prepare("SELECT id FROM contact_details WHERE id = ?");
    $stmt->execute([$contactId]);
    if (!$stmt->fetch()) {
        throw new Exception('Contact not found');
    }

    // Delete the category
    $stmt = $pdo->prepare("DELETE FROM contact_details WHERE id = ?");
    $stmt->execute([$contactId]);

    // Commit transaction
    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Contact deleted successfully']);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}