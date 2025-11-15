<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    // Validate required fields
    if (empty($_POST['id'])) throw new Exception('Faculty ID is required');
    if (empty($_POST['name'])) throw new Exception('Name is required');
    if (empty($_POST['title'])) throw new Exception('Title is required');
    if (empty($_POST['specialization'])) throw new Exception('Specialization is required');
    if (empty($_POST['experience'])) throw new Exception('Experience is required');

    // Get current faculty data
    $stmt = $pdo->prepare("SELECT photo FROM faculty WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    $currentFaculty = $stmt->fetch();

    $fileName = $currentFaculty['photo'];

    // Handle new photo upload if provided
    if (!empty($_FILES['photo']['name'])) {
        $photo = $_FILES['photo'];
        $fileName = time() . '_' . $photo['name'];
        $targetPath = '../../uploads/faculty/' . $fileName;

        // Validate image
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($photo['type'], $allowedTypes)) {
            throw new Exception('Invalid file type. Only JPG, PNG and GIF are allowed');
        }

        if (!move_uploaded_file($photo['tmp_name'], $targetPath)) {
            throw new Exception('Failed to upload photo');
        }

        // Delete old photo
        if ($currentFaculty['photo']) {
            $oldFile = '../../uploads/faculty/' . $currentFaculty['photo'];
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
    }

    // Update faculty
    $stmt = $pdo->prepare("
        UPDATE faculty SET 
            title = ?,
            name = ?,
            specialization = ?,
            experience = ?,
            photo = ?,
            linkedin = ?,
            twitter = ?,
            display_order = ?,
            updated_at = NOW()
        WHERE id = ?
    ");

    $result = $stmt->execute([
        $_POST['title'],
        $_POST['name'],
        $_POST['specialization'],
        $_POST['experience'],
        $fileName,
        isset($_POST['linkedin']) ? $_POST['linkedin']: null,
        isset($_POST['twitter']) ? $_POST['twitter']: null,
        isset($_POST['display_order']) ? $_POST['display_order']: 0,
        $_POST['id']
    ]);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Faculty updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update faculty');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}