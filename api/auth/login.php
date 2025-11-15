<?php
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
//$loginImage = isset($_POST['loginImage']) ? $_POST['loginImage'] : '';

// Validate credentials
$stmt = $pdo->prepare("SELECT id,name,profile_pic, password FROM admin_users WHERE email = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    // Save login image
    //$imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $loginImage));
    //$imageName = 'login_' . time() . '_' . $user['id'] . '.jpg';
    //$imagePath = '../../uploads/login_images/' . $imageName;
    //file_put_contents($imagePath, $imageData);

    // Record login history
    $stmt = $pdo->prepare("INSERT INTO admin_login_history (admin_id, login_time, ip_address, user_agent, status) VALUES (?, NOW(), ?, ?, 'success')");
    $stmt->execute([
        $user['id'],
        $_SERVER['REMOTE_ADDR'],
        $_SERVER['HTTP_USER_AGENT']
    ]);

    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_id'] = $user['id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['profile_pic'] = $user['profile_pic'];
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid credentials']);
}