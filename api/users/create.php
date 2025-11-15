<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    // Validate required fields
    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['password'])) {
        throw new Exception('Required fields are missing');
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE email = ?");
    $stmt->execute([$_POST['email']]);
    if ($stmt->fetch()) {
        throw new Exception('Email already exists');
    }

    // Handle file upload
    $profile_pic = '';
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $file_ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
        
        if (!in_array($file_ext, $allowed)) {
            throw new Exception('Invalid file type');
        }

        $upload_dir = '../../uploads/profile_pics/';
        $file_name = 'profile_' . time() . '.' . $file_ext;
        
        if (!move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_dir . $file_name)) {
            throw new Exception('Failed to upload file');
        }
        
        $profile_pic = $file_name;
    }

    // Insert user
    $stmt = $pdo->prepare("INSERT INTO admin_users (name, email, password, gender, profile_pic, city, about,type) VALUES (?, ?, ?, ?, ?, ?, ?,?)");
    $stmt->execute([
        $_POST['name'],
        $_POST['email'],
        password_hash($_POST['password'], PASSWORD_DEFAULT),
        $_POST['gender'],
        $profile_pic,
        $_POST['city'],
        $_POST['about'],
        $_POST['type']
    ]);

    echo json_encode([
        'status' => 'success',
        'message' => 'User created successfully'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}