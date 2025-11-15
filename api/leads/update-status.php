<?php
header('Content-Type: application/json');
require_once('../../config/database.php');

try {
    if (!isset($_POST['lead_id']) || !isset($_POST['status'])) {
        throw new Exception('Required parameters are missing');
    }

    $lead_id = intval($_POST['lead_id']);
    $status = $_POST['status'];

    $valid_statuses = ['new', 'in_progress', 'contacted', 'qualified', 
                       'not_interested', 'converted', 'on_hold'];
    if (!in_array($status, $valid_statuses)) {
        throw new Exception('Invalid status value');
    }

    // Start transaction
    $pdo->beginTransaction();

    // Update the lead status
    $stmt = $pdo->prepare("UPDATE contact_enquiries SET status = ?, updated_at = NOW() WHERE id = ?");
    $result = $stmt->execute([$status, $lead_id]);

    // If status is converted, create user account
    if ($result && $status === 'converted') {
        // Get lead details
        $stmt = $pdo->prepare("SELECT * FROM contact_enquiries WHERE id = ?");
        $stmt->execute([$lead_id]);
        $lead = $stmt->fetch();

        // Check if email already exists in users table
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$lead['email']]);
        if (!$stmt->fetch()) {
            // Generate random password
            $password = bin2hex(random_bytes(8));
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert into users table
            $stmt = $pdo->prepare("
                INSERT INTO users (
                    name, email, phone, password, status, created_at, updated_at
                ) VALUES (
                    ?, ?, ?, ?, 1, NOW(), NOW()
                )
            ");
            
            $stmt->execute([
                $lead['name'],
                $lead['email'],
                $lead['phone'],
                $hashed_password
            ]);

            // Send welcome email with password
            // TODO: Implement email sending functionality
            
            $pdo->commit();
            echo json_encode([
                'status' => 'success',
                'message' => 'Lead converted and user account created successfully'
            ]);
        } else {
            $pdo->commit();
            echo json_encode([
                'status' => 'success',
                'message' => 'Lead converted but user account already exists'
            ]);
        }
    } else {
        $pdo->commit();
        echo json_encode(['status' => 'success']);
    }

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}