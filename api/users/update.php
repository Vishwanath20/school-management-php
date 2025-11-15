<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        throw new Exception('User ID is required');
    }

    $userId = $_POST['id'];
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $city = isset($_POST['city']) ? $_POST['city'] : '';
    $about = isset($_POST['about']) ? $_POST['about'] : '';
    $type = isset($_POST['type']) ? $_POST['type'] : '';

    // Check if email exists for other users
    $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $userId]);
    if ($stmt->fetch()) {
        throw new Exception('Email already exists');
    }

    // Start building update query
    $updateFields = ['name = ?', 'email = ?', 'gender = ?', 'city = ?', 'about = ?', 'type = ?'];
    $params = [$name, $email, $gender, $city, $about, $type];

    // Handle password update if provided
    if (!empty($_POST['password'])) {
        $updateFields[] = 'password = ?';
        $params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    // Handle profile picture upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $file_ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
        
        if (!in_array($file_ext, $allowed)) {
            throw new Exception('Invalid file type');
        }

        // Delete old profile picture
        $stmt = $pdo->prepare("SELECT profile_pic FROM admin_users WHERE id = ?");
        $stmt->execute([$userId]);
        $oldPic = $stmt->fetchColumn();
        
        if ($oldPic) {
            $oldPicPath = '../../uploads/profile_pics/' . $oldPic;
            if (file_exists($oldPicPath)) {
                unlink($oldPicPath);
            }
        }

        // Upload new picture
        $fileName = 'profile_' . time() . '_' . $userId . '.' . $file_ext;
        $uploadPath = '../../uploads/profile_pics/' . $fileName;
        
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $uploadPath)) {
            $updateFields[] = 'profile_pic = ?';
            $params[] = $fileName;
        }
    }

    // Add user ID to params
    $params[] = $userId;

    // Update user
    $sql = "UPDATE admin_users SET " . implode(', ', $updateFields) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($params);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'User updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update user');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}