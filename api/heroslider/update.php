<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    // Validate required fields
    if (empty($_POST['id'])) {
        throw new Exception('Slider ID is required');
    }

    // Prepare update data
    $updateData = [
        'title' => isset($_POST['title']) ? $_POST['title'] : '',
        'subtitle' => isset($_POST['subtitle']) ? $_POST['subtitle'] : '',
        'description' => isset($_POST['description']) ? $_POST['description'] : '',
        'button_text' => isset($_POST['button_text']) ? $_POST['button_text'] : '',
        'display_order' => isset($_POST['display_order']) ? $_POST['display_order'] : '',
        'button_link' => isset($_POST['button_link']) ? $_POST['button_link'] : ''
    ];

    // Handle image upload if new image is provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        
        if (!in_array($file_ext, $allowed)) {
            throw new Exception('Invalid file type. Allowed types: ' . implode(', ', $allowed));
        }

        // Get old image to delete
        $stmt = $pdo->prepare("SELECT image FROM hero_sliders WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $oldImage = $stmt->fetchColumn();

        // Upload new image
        $fileName = 'slider_' . time() . '.' . $file_ext;
        $uploadPath = '../../uploads/sliders/' . $fileName;
        
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            throw new Exception('Failed to upload image');
        }

        // Delete old image if exists
        if ($oldImage && file_exists('../../uploads/sliders/' . $oldImage)) {
            unlink('../../uploads/sliders/' . $oldImage);
        }

        $updateData['image'] = $fileName;
    }

    // Build update query
    $sql = "UPDATE hero_sliders SET ";
    $params = [];
    foreach ($updateData as $key => $value) {
        $sql .= "$key = ?, ";
        $params[] = $value;
    }
    $sql .= "updated_at = NOW() WHERE id = ?";
    $params[] = $_POST['id'];

    // Execute update
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($params);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Slider updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update slider');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}