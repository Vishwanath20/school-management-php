<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    $data = $_POST;
    
    // Validate required fields
    $required = ['name', 'email', 'phone', 'password'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("All fields are required");
        }
    }
    
    // Validate email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format");
    }
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    if ($stmt->fetch()) {
        throw new Exception("Email already registered");
    }
    
    // Hash password
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    
    // Insert user
    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, phone, password, status, created_at) 
        VALUES (?, ?, ?, ?, 1, NOW())
    ");
    
    $stmt->execute([
        $data['name'],
        $data['email'],
        $data['phone'],
        $hashedPassword
    ]);
    
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}