<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    if (empty($_POST['id'])) {
        throw new Exception('Course ID is required');
    }

    // Start transaction
    $pdo->beginTransaction();

    // Get course thumbnail and curriculum_pdf before deletion
    $stmt = $pdo->prepare("SELECT thumbnail, curriculum_pdf FROM courses WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch as associative array

    if ($course) {
        // --- IMPORTANT: Delete records from child tables that reference 'courses' ---

        // 1. Delete related course_installments records (NEWLY ADDED)
        $stmt = $pdo->prepare("DELETE FROM course_installments WHERE course_id = ?");
        $stmt->execute([$_POST['id']]);

        // 2. Delete related batch records (already present)
        $stmt = $pdo->prepare("DELETE FROM batches WHERE course_id = ?");
        $stmt->execute([$_POST['id']]);

        // Add more child tables here if they exist and have foreign keys to 'courses'
        // Example: If 'enrollments' table references 'courses', delete from it too:
        // $stmt = $pdo->prepare("DELETE FROM enrollments WHERE course_id = ?");
        // $stmt->execute([$_POST['id']]);

        // Then delete the course itself
        $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
        $result = $stmt->execute([$_POST['id']]);

        if ($result) {
            // Delete thumbnail file
            if (!empty($course['thumbnail'])) { // Use !empty for robustness
                $thumbnailPath = '../../uploads/courses/' . $course['thumbnail'];
                if (file_exists($thumbnailPath)) {
                    unlink($thumbnailPath);
                }
            }

            // Delete curriculum PDF file (Good practice to clean up this too)
            if (!empty($course['curriculum_pdf'])) {
                $curriculumPath = '../../uploads/courses/curriculum/' . $course['curriculum_pdf'];
                if (file_exists($curriculumPath)) {
                    unlink($curriculumPath);
                }
            }

            // Commit transaction
            $pdo->commit();

            echo json_encode([
                'status' => 'success',
                'message' => 'Course, related batches, and installments deleted successfully'
            ]);
        } else {
            throw new Exception('Failed to delete course from courses table.');
        }
    } else {
        throw new Exception('Course not found.');
    }

} catch (Exception $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}