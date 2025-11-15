<?php
include('../../config/database.php');
include('../include/header.php');
// Get exam ID
$exam_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$exam_id) {
    echo "<script>window.location.href = 'details.php';</script>";
    exit;
}

// Fetch exam details
$query = "SELECT e.*, c.title as course_title, b.name as batch_name 
         FROM exams e 
         LEFT JOIN courses c ON e.course_id = c.id 
         LEFT JOIN batches b ON e.batch_id = b.id 
         WHERE e.id = $exam_id";
$result = $pdo->query($query);
$exam = $result->fetch();

if (!$exam) {
    echo "<script>window.location.href = 'details.php';</script>";
    exit;
}

// Fetch selected questions
$query = "SELECT q.* FROM questions q 
         INNER JOIN exam_questions eq ON q.id = eq.question_id 
         WHERE eq.exam_id = $exam_id";
$result = $pdo->query($query);
$selected_questions = [];
while ($row = $result->fetch()) {
    $selected_questions[] = $row;
}
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Edit Exam</h4>
                        <a href="details.php" class="btn btn-primary">List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="editExamForm" class="forms-sample">
                        <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">

                        <div class="form-group">
                            <label for="title">Exam Title</label>
                            <input type="text" class="form-control" id="title" name="title"
                                value="<?php echo htmlspecialchars($exam['title']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="courseId">Course</label>
                            <select class="form-control" id="courseId" name="course_id" required>
                                <option value="">Select Course</option>
                                <?php
                                        $sql = "SELECT id, title FROM courses WHERE status = 1";
                                        $result = $pdo->query($sql);
                                        while ($row = $result->fetch()) {
                                            $selected = $row['id'] == $exam['course_id'] ? 'selected' : '';
                                            echo "<option value='{$row['id']}' {$selected}>{$row['title']}</option>";
                                        }
                                        ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="batchId">Batch</label>
                            <select class="form-control" id="batchId" name="batch_id" required>
                                <option value="">Select Batch</option>
                                <?php
                                        if ($exam['course_id']) {
                                            $sql = "SELECT id, name FROM batches WHERE course_id = {$exam['course_id']} AND status = 1";
                                            $result = $pdo->query($sql);
                                            while ($row = $result->fetch()) {
                                                $selected = $row['id'] == $exam['batch_id'] ? 'selected' : '';
                                                echo "<option value='{$row['id']}' {$selected}>{$row['name']}</option>";
                                            }
                                        }
                                        ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="examDate">Exam Date</label>
                            <input type="datetime-local" class="form-control" id="examDate" name="exam_date"
                                value="<?php echo date('Y-m-d\TH:i', strtotime($exam['exam_date'])); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="duration">Duration (minutes)</label>
                            <input type="number" class="form-control" id="duration" name="duration"
                                value="<?php echo $exam['duration']; ?>" required min="1">
                        </div>

                        <div class="form-group">
                            <label>Questions</label>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <select class="form-control" id="subjectFilter">
                                        <option value="">Select Subject</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control" id="topicFilter">
                                        <option value="">Select Topic</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control" id="difficultyFilter">
                                        <option value="">Select Difficulty</option>
                                        <option value="easy">Easy</option>
                                        <option value="medium">Medium</option>
                                        <option value="hard">Hard</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-primary" id="filterQuestions">Filter</button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Select</th>
                                            <th>Question</th>
                                            <th>Subject</th>
                                            <th>Topic</th>
                                            <th>Difficulty</th>
                                            <th>Marks</th>
                                        </tr>
                                    </thead>
                                    <tbody id="questionsTableBody">
                                        <!-- Questions will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">Update Exam</button>
                        <a href="details.php" class="btn btn-light">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../include/footer.php'); ?>
<script>
$(document).ready(function() {
    // Load subjects when course changes
    $('#courseId').change(function() {
        const courseId = $(this).val();
        // Load batches
        if (courseId) {
            $.get('../../api/batches/get_batches_by_course.php', {
                course_id: courseId
            }, function(response) {
                const data = response; // Assuming response is already parsed JSON
                let options = '<option value="">Select Batch</option>';
                const currentBatchId =
                "<?php echo $exam['batch_id']; ?>"; // Get the current batch ID from PHP

                if (data.success && data.batches) {
                    data.batches.forEach(batch => {
                        const isSelected = (batch.id == currentBatchId) ? 'selected' :
                            '';
                        options +=
                            `<option value="${batch.id}" ${isSelected}>${batch.name}</option>`;
                    });
                }
                $('#batchId').html(options);
            });

            // Load subjects
            $.get('../../api/questions/get_subjects.php', {
                course_id: courseId
            }, function(response) {
                const data = response; // Assuming response is already parsed JSON
                let options = '<option value="">Select Subject</option>';
                if (data.status === 'success' && data.subjects) {
                    data.subjects.forEach(subject => {
                        options +=
                            `<option value="${subject.id}">${subject.name}</option>`;
                    });
                }
                $('#subjectFilter').html(options);
            });
        } else {
            $('#batchId').html('<option value="">Select Batch</option>');
            $('#subjectFilter').html('<option value="">Select Subject</option>');
        }
    });

    // Load topics when subject changes
    $('#subjectFilter').change(function() {
        const subjectId = $(this).val();
        if (subjectId) {
            $.get('../../api/questions/get_topics.php', {
                subject_id: subjectId
            }, function(response) {
                const data = JSON.parse(response); // API returns string, so parse it
                let options = '<option value="">Select Topic</option>';
                if (data.status === 'success' && data.topics) {
                    data.topics.forEach(topic => {
                        options += `<option value="${topic.id}">${topic.name}</option>`;
                    });
                }
                $('#topicFilter').html(options);
            });
        } else {
            $('#topicFilter').html('<option value="">Select Topic</option>');
        }
    });

    // Filter questions
    $('#filterQuestions').click(function() {
        const filters = {
            subject_id: $('#subjectFilter').val(),
            topic_id: $('#topicFilter').val(),
            difficulty: $('#difficultyFilter').val()
        };

        $.get('../../api/questions/get-filter-guestion.php', filters, function(response) {
            const data = response; // Assuming response is already parsed JSON
            let html = '';
            if (data.status === 'success' && data.questions) {
                data.questions.forEach(question => {
                    const isSelected = selectedQuestions.some(q => q.id === question
                    .id);
                    html += `
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input question-select" 
                                           value="${question.id}" ${isSelected ? 'checked' : ''}>
                                </div>
                            </td>
                            <td>${question.question_text}</td>
                            <td>${question.subject_name}</td>
                            <td>${question.topic_name}</td>
                            <td>${question.difficulty_level}</td>
                            <td>${question.marks}</td>
                        </tr>
                    `;
                });
            } else {
                html = `<tr><td colspan="6" class="text-center">No questions found</td></tr>`;
            }
            $('#questionsTableBody').html(html);
        });
    });

    // Store selected questions
    let selectedQuestions = <?php echo json_encode($selected_questions); ?>;

    // Handle form submission
    $('#editExamForm').submit(function(e) {
        e.preventDefault();

        const selectedQuestionIds = [];
        $('.question-select:checked').each(function() {
            selectedQuestionIds.push($(this).val());
        });

        const formData = {
            exam_id: $('input[name="exam_id"]').val(),
            title: $('#title').val(),
            course_id: $('#courseId').val(),
            batch_id: $('#batchId').val(),
            exam_date: $('#examDate').val(),
            duration: $('#duration').val(),
            question_ids: selectedQuestionIds
        };

        $.ajax({
            url: '../../api/exams/update.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    toastr.success('Exam updated successfully');
                    setTimeout(() => window.location.href = 'details.php', 1000);
                } else {
                    toastr.error(response.message || 'Error updating exam');
                }
            },
            error: function() {
                toastr.error('Error updating exam');
            }
        });
    });

    // Initial load of questions
    if ($('#courseId').val()) {
        $('#courseId').trigger('change');
    }
});
</script>