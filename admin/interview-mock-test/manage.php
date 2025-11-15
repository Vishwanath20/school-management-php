<?php
require_once('../../config/database.php');
include('../include/header.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$mock_test = null;
$selected_course_id = 0;
$selected_batch_id = 0;
$batches = [];

if ($id > 0) {
    // Fetch existing mock test data
    $stmt = $pdo->prepare("SELECT * FROM interview_mock_tests WHERE id = ?");
    $stmt->execute([$id]);
    $mock_test = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($mock_test) {
        $selected_course_id = $mock_test['course_id'];
        $selected_batch_id = $mock_test['batch_id'];

        // Fetch batches for the selected course
        $stmt_batches = $pdo->prepare("SELECT id, name FROM batches WHERE course_id = ? AND status = 1");
        $stmt_batches->execute([$selected_course_id]);
        $batches = $stmt_batches->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Get all courses
$stmt = $pdo->prepare("SELECT id, title FROM courses WHERE status = 1");
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                 <div class="card-header" style="display:flex;justify-content:space-between;">
                     <h4 class="card-title"><?php echo $id ? 'Edit AI Interview Mock Test' : 'Create AI Interview Mock Test'; ?></h4>
                    <div>
                        <a href="details.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-plus btn-icon-prepend"></i>List</a>
                    </div>
                </div>
                <div class="card-body">
                   
                    <form class="forms-sample" id="mockTestForm">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <div class="form-group">
                            <label for="course">Select Course</label>
                            <select class="form-control" id="course" name="course_id" required>
                                <option value="">Select Course</option>
                                <?php foreach($courses as $course): ?>
                                    <option value="<?php echo $course['id']; ?>" <?php echo ($selected_course_id == $course['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($course['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="batch">Select Batch</label>
                            <select class="form-control" id="batch" name="batch_id" required <?php echo $selected_course_id ? '' : 'disabled'; ?>>
                                <option value="">Select Batch</option>
                                <?php foreach($batches as $batch): ?>
                                    <option value="<?php echo $batch['id']; ?>" <?php echo ($selected_batch_id == $batch['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($batch['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="examTitle">Test Title</label>
                            <input type="text" class="form-control" id="examTitle" name="title" value="<?php echo $mock_test ? htmlspecialchars($mock_test['title']) : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="examDate">Test Date</label>
                            <input type="datetime-local" class="form-control" id="examDate" name="test_date" value="<?php echo $mock_test ? date('Y-m-d\TH:i', strtotime($mock_test['test_date'])) : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="duration">Duration (in minutes)</label>
                            <input type="number" class="form-control" id="duration" name="duration" value="<?php echo $mock_test ? htmlspecialchars($mock_test['duration']) : '1'; ?>" required min="1">
                        </div>

                        <div class="form-group">
                            <label for="aiPrompt">AI Prompt</label>
                            <textarea class="form-control" id="aiPrompt" name="ai_prompt" rows="5" required placeholder="e.g., 'Conduct an interview for a software engineer role focusing on data structures and algorithms.'"><?php echo $mock_test ? htmlspecialchars($mock_test['ai_prompt']) : ''; ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary mr-2"><?php echo $id ? 'Update Mock Test' : 'Create Mock Test'; ?></button>
                        <button type="button" class="btn btn-light" onclick="window.location.href='details.php'">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../include/footer.php"; ?>
<script>
$(document).ready(function() {
    // Function to load batches
    function loadBatches(courseId, selectedBatchId = 0) {
        const batchSelect = $('#batch');
        batchSelect.empty().append('<option value="">Select Batch</option>');
        batchSelect.prop('disabled', true);

        if(courseId) {
            $.ajax({
                url: '../../api/batches/get_batches_by_course.php',
                type: 'GET',
                data: { course_id: courseId },
                success: function(response) {
                    const data = response;
                    if(data.success) {
                        data.batches.forEach(function(batch) {
                            const selected = (selectedBatchId == batch.id) ? 'selected' : '';
                            batchSelect.append(`<option value="${batch.id}" ${selected}>${batch.name}</option>`);
                        });
                        batchSelect.prop('disabled', false);
                    } else {
                        toastr.error('Error loading batches');
                    }
                },
                error: function() {
                    toastr.error('Error loading batches');
                }
            });
        }
    }

    // Initial load of batches if course is pre-selected (for edit mode)
    const initialCourseId = $('#course').val();
    const initialBatchId = <?php echo $selected_batch_id; ?>;
    if (initialCourseId && initialBatchId) {
        loadBatches(initialCourseId, initialBatchId);
    } else if (initialCourseId) {
        loadBatches(initialCourseId);
    }

    // Load batches when course is selected
    $('#course').change(function() {
        const courseId = $(this).val();
        loadBatches(courseId);
    });

    // Submit form
    $('#mockTestForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serializeArray();
        const mockTestId = $('input[name="id"]').val();
        const apiUrl = mockTestId > 0 ? '../../api/interview-mock-tests/update.php' : '../../api/interview-mock-tests/create.php';

        $.ajax({
            url: apiUrl,
            type: 'POST',
            data: formData,
            success: function(response) {
                const data = response;
                if(data.status === 'success') {
                    toastr.success('Mock Test saved successfully');
                    setTimeout(function() {
                        window.location.href = 'details.php';
                    }, 1500);
                } else {
                    toastr.error(data.message || 'Error saving mock test');
                }
            },
            error: function() {
                toastr.error('Error saving mock test');
            }
        });
    });
});
</script>
