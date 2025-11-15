<?php
require_once('../../config/database.php');

header('Content-Type: application/json');

try {
    if (empty($_POST['id'])) {
        throw new Exception('ID is required');
    }

    // Validate required fields
    $required_fields = ['name', 'email', 'phone', 'dob', 'address', 'city', 'state', 'pincode', 'status'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("$field is required");
        }
    }

    // Check if email exists for other users
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$_POST['email'], $_POST['id']]);
    if ($stmt->fetch()) {
        throw new Exception('Email already exists');
    }

    // Get current user data
    $stmt = $pdo->prepare("SELECT profile_image FROM users WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    $current_user = $stmt->fetch();

    // Handle profile image upload
    $profile_image = $current_user['profile_image'];
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

        // Delete old image
        if ($current_user['profile_image'] && file_exists('../../uploads/profiles/' . $current_user['profile_image'])) {
            unlink('../../uploads/profiles/' . $current_user['profile_image']);
        }
    }

    // Build update query
    $sql = "UPDATE users SET 
            name = ?, 
            email = ?, 
            phone = ?, 
            dob = ?, 
            address = ?, 
            city = ?, 
            state = ?, 
            pincode = ?, 
            status = ?, 
            profile_image = ?,
            updated_at = NOW()";

    $params = [
        $_POST['name'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['dob'],
        $_POST['address'],
        $_POST['city'],
        $_POST['state'],
        $_POST['pincode'],
        $_POST['status'],
        $profile_image
    ];

    // Add password update if provided
    if (!empty($_POST['password'])) {
        $sql .= ", password = ?";
        $params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    $sql .= " WHERE id = ?";
    $params[] = $_POST['id'];

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['success' => true, 'message' => 'Candidate updated successfully']);

} catch (Exception $e) {
    // Delete newly uploaded image if update fails
    if (isset($profile_image) && $profile_image !== $current_user['profile_image'] && 
        file_exists('../../uploads/profiles/' . $profile_image)) {
        unlink('../../uploads/profiles/' . $profile_image);
    }
    
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}