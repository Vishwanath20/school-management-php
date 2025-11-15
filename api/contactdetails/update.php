<?php
require_once '../../config/database.php';
header('Content-Type: application/json');
$id = $_POST['id'] ?? false;

try {
    // Validate required fields
    if (empty($_POST['address'])) throw new Exception('Address is required');
    if (empty($_POST['phone'])) throw new Exception('Phone number is required');
    if (empty($_POST['email'])) throw new Exception('Email is required');
    if (empty($_POST['working_hours'])) throw new Exception('Working hours is required');
    if (empty($_POST['map_embed'])) throw new Exception('Map embed URL is required');

    // Validate email format
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Check if record exists
    // $stmt = $pdo->query("SELECT id FROM contact_details LIMIT 1");
    // $existing = $stmt->fetch();
    if ($id) {
        // Update existing record
        $stmt = $pdo->prepare("
            UPDATE contact_details SET 
                address = ?,
                phone = ?,
                email = ?,
                working_hours = ?,
                map_embed = ?,
                status=?,
                updated_at = NOW()
            WHERE id = ?
        ");

        $result = $stmt->execute([
            $_POST['address'],
            $_POST['phone'],
            $_POST['email'],
            $_POST['working_hours'],
            $_POST['map_embed'],
            $_POST['status'],
            $id
        ]);
    } else {
        // Insert new record
        $stmt = $pdo->prepare("
            INSERT INTO contact_details (
                address, phone, email, working_hours,status, map_embed,created_at,updated_at
            ) VALUES (?, ?, ?, ?, ?, ?,NOW(),NOW())
        ");

        $result = $stmt->execute([
            $_POST['address'],
            $_POST['phone'],
            $_POST['email'],
            $_POST['working_hours'],
            $_POST['status'],
            $_POST['map_embed']
        ]);
    }

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Contact details updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update contact details');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}