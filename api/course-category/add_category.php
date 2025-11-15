<?php
require_once('../../config/database.php');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['title'], $_POST['status'])) {
        $title = $_POST['title'];
        $slug = $_POST['slug'];
        $status = $_POST['status'];

        try {
            $stmt = $pdo->prepare("INSERT INTO course_categories (title,slug, status,added_on) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$title, $slug, $status]);

            $response['success'] = true;
            $response['message'] = 'Category added successfully!';
        } catch (Exception $e) {
            $response['message'] = 'Failed to add category: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Invalid input data!';
    }
} else {
    $response['message'] = 'Invalid request method!';
}

echo json_encode($response);
?>