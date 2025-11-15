<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    // Validate required fields
    if (empty($_POST['id'])) throw new Exception('Course ID is required');
    if (empty($_POST['title'])) throw new Exception('Title is required');
    if (empty($_POST['description'])) throw new Exception('Description is required');
    // if (empty($_POST['type'])) throw new Exception('Course type is required');
    if (empty($_POST['category_id'])) throw new Exception('Category is required');
    if (empty($_POST['price'])) throw new Exception('Price is required');
    if (empty($_POST['original_price'])) throw new Exception('Original price is required');
    //if (empty($_POST['is_price_display'])) throw new Exception('Price display is required');
    // if (empty($_POST['start_date'])) throw new Exception('Start date is required');
    // if (empty($_POST['end_date'])) throw new Exception('End date is required');

    // Get current course data
    $stmt = $pdo->prepare("SELECT thumbnail, curriculum_pdf FROM courses WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    $currentCourse = $stmt->fetch();

    $fileName = $currentCourse['thumbnail'];
    $curriculumFileName = $currentCourse['curriculum_pdf'];

    // Handle new thumbnail upload if provided
    if (!empty($_FILES['thumbnail']['name'])) {
        $thumbnail = $_FILES['thumbnail'];
        $fileName = time() . '_' . $thumbnail['name'];
        $targetPath = '../../uploads/courses/' . $fileName;

        if (!move_uploaded_file($thumbnail['tmp_name'], $targetPath)) {
            throw new Exception('Failed to upload thumbnail');
        }

        // Delete old thumbnail
        if ($currentCourse['thumbnail']) {
            $oldFile = '../../uploads/courses/' . $currentCourse['thumbnail'];
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
    }

    // Handle new curriculum PDF upload if provided
    if (!empty($_FILES['curriculum_pdf']['name'])) {
        $curriculum = $_FILES['curriculum_pdf'];
        $curriculumFileName = time() . '_' . $curriculum['name'];
        $curriculumPath = '../../uploads/courses/curriculum/' . $curriculumFileName;

        if (!move_uploaded_file($curriculum['tmp_name'], $curriculumPath)) {
            throw new Exception('Failed to upload curriculum PDF');
        }

        // Delete old curriculum PDF
        if ($currentCourse['curriculum_pdf']) {
            $oldPdf = '../../uploads/courses/curriculum/' . $currentCourse['curriculum_pdf'];
            if (file_exists($oldPdf)) {
                unlink($oldPdf);
            }
        }
    }

    // Process video links
    $videoLinks = isset($_POST['video_links']) ? array_filter($_POST['video_links']) : [];
    $videoLinksJson = json_encode($videoLinks);

    // Update course
    $stmt = $pdo->prepare("
        UPDATE courses SET 
            title = ?,
            description = ?,
            thumbnail = ?,
            curriculum_pdf = ?,
            category_id = ?,
            price = ?,
            original_price = ?,
            is_price_display = ?,
            video_links = ?,
            updated_at = NOW()
        WHERE id = ?
    ");

    $result = $stmt->execute([
        $_POST['title'],
        $_POST['description'],
        $fileName,
        $curriculumFileName,
        $_POST['category_id'],
        $_POST['price'],
        $_POST['original_price'],
        $_POST['is_price_display'],
        // $_POST['start_date'],
        // $_POST['end_date'],
        $videoLinksJson,
        $_POST['id']
    ]);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Course updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update course');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}