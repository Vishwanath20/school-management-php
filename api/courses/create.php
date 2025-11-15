<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    // Validate required fields
    if (empty($_POST['title'])) throw new Exception('Title is required');
    if (empty($_POST['description'])) throw new Exception('Description is required');
    // if (empty($_POST['type'])) throw new Exception('Course type is required');
    if (empty($_POST['category_id'])) throw new Exception('Category is required');
    if (empty($_POST['price'])) throw new Exception('Price is required');
    if (empty($_POST['original_price'])) throw new Exception('Original price is required');
    // if (empty($_POST['start_date'])) throw new Exception('Start date is required');
    // if (empty($_POST['end_date'])) throw new Exception('End date is required');
    if (empty($_FILES['thumbnail'])) throw new Exception('Thumbnail is required');
    // Curriculum PDF is optional

    // Handle thumbnail upload
    $thumbnail = $_FILES['thumbnail'];
    $thumbnailName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $thumbnail['name']);
    $thumbnailPath = '../../uploads/courses/' . $thumbnailName;

    if (!move_uploaded_file($thumbnail['tmp_name'], $thumbnailPath)) {
        throw new Exception('Failed to upload thumbnail');
    }

    // Handle curriculum PDF upload (optional)
    $curriculumName = null;
    if (!empty($_FILES['curriculum_pdf']) && $_FILES['curriculum_pdf']['error'] === UPLOAD_ERR_OK) {
        $curriculum = $_FILES['curriculum_pdf'];
        $curriculumName = time() . '_' . $curriculum['name'];
        $curriculumPath = '../../uploads/courses/curriculum/' . $curriculumName;
        if (!move_uploaded_file($curriculum['tmp_name'], $curriculumPath)) {
            unlink($thumbnailPath); // Remove thumbnail if PDF upload fails
            throw new Exception('Failed to upload curriculum PDF');
        }
    }

    // Process video links
    $videoLinks = isset($_POST['video_links']) ? array_filter($_POST['video_links']) : [];
    $videoLinksJson = json_encode($videoLinks);

    // Insert course
    $stmt = $pdo->prepare("
        INSERT INTO courses (
            title, description, thumbnail, curriculum_pdf, category_id,
            price, original_price,is_price_display,video_links, created_at
        ) VALUES (?, ?, ?, ?, ?,?, ?, ?, ?, NOW())
    ");

    $result = $stmt->execute([
        $_POST['title'],
        $_POST['description'],
        $thumbnailName,
        $curriculumName,
        $_POST['category_id'],
        $_POST['price'],
        $_POST['original_price'],
        $_POST['is_price_display'],
        // $_POST['start_date'],
        // $_POST['end_date'],
        $videoLinksJson
    ]);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Course added successfully'
        ]);
    } else {
        // Clean up uploaded files if DB insert fails
        unlink($thumbnailPath);
        unlink($curriculumPath);
        throw new Exception('Failed to add course');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}