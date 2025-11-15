<?php
require_once '../../config/database.php';

header('Content-Type: application/json');

try {
    if (empty($_POST['email'])) {
        throw new Exception('Email is required');
    }

    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if (!$email) {
        throw new Exception('Invalid email format');
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        throw new Exception('You are already subscribed!');
    }

    // Insert new subscriber
    $stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)");
    $result = $stmt->execute([$email]);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Failed to subscribe');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}