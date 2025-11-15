<?php
require_once('../../config/database.php');

header('Content-Type: application/json');

try {
    // Validate required fields
    $required_fields = ['name', 'email', 'phone', 'password', 'dob', 'address', 'city', 'state', 'pincode', 'status'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("$field is required");
        }
    }

    // Handle profile image upload
    $profile_image = null;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            throw new Exception('Invalid image format');
        }

        $profile_image = time() . '_' . $filename;
        $upload_path = '../../uploads/profiles/' . $profile_image;
        
        if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
            throw new Exception('Failed to upload image');
        }
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$_POST['email']]);
    if ($stmt->fetch()) {
        throw new Exception('Email already exists');
    }

    // Insert new user
    $sql = "INSERT INTO users (name, email, phone, password, dob, address, city, state, pincode, status, profile_image, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['name'],
        $_POST['email'],
        $_POST['phone'],
        password_hash($_POST['password'], PASSWORD_DEFAULT),
        $_POST['dob'],
        $_POST['address'],
        $_POST['city'],
        $_POST['state'],
        $_POST['pincode'],
        $_POST['status'],
        $profile_image
    ]);

    echo json_encode(['success' => true, 'message' => 'Candidate added successfully']);

} catch (Exception $e) {
    // Delete uploaded image if insertion fails
    if (isset($profile_image) && file_exists('../../uploads/profiles/' . $profile_image)) {
        unlink('../../uploads/profiles/' . $profile_image);
    }
    
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}