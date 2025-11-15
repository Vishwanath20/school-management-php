<?php
require_once('../../config/database.php');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'], $_POST['title'], $_POST['status'])) {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $slug = $_POST['slug'];
        $status = $_POST['status'];

        try {
            $stmt = $pdo->prepare("UPDATE course_categories SET title = ?, slug = ?, status = ? WHERE id = ?");
            $stmt->execute([$title, $slug, $status, $id]);

            $response['success'] = true;
            $response['message'] = 'Category updated successfully!';
        } catch (Exception $e) {
            $response['message'] = 'Failed to update category: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Invalid input data!';
    }
} else {
    $response['message'] = 'Invalid request method!';
}

echo json_encode($response);
?>