<?php
require_once '../../config/database.php';
header('Content-Type: application/json');


try {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Validate input
    if (empty($email) || empty($password)) {
        throw new Exception("All fields are required");
    }
    
    // Get user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($password, $user['password'])) {
        throw new Exception("Invalid email or password");
    }
    
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    
    // Check if there's a redirect URL
    $redirect = isset($_SESSION['redirect_after_login']) 
        ? $_SESSION['redirect_after_login'] 
        : 'index.php';
    unset($_SESSION['redirect_after_login']);
    
    echo json_encode([
        'success' => true,
        'redirect' => $redirect
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}