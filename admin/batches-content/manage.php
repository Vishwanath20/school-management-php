<?php 
require_once('../../config/database.php');
include('../include/header.php');

// Fetch content data if editing
$content = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM batch_content WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $content = $stmt->fetch();
}

// Fetch batches for dropdown
$stmt = $pdo->query("SELECT id, name FROM batches WHERE status = 1 ORDER BY name");
$batches = $stmt->fetchAll();

// Fetch subjects for dropdown
$stmt = $pdo->query("SELECT id, name FROM subjects WHERE status = 1 ORDER BY name");
$subjects = $stmt->fetchAll();


$stmt = $pdo->query("SELECT id, title FROM courses WHERE status = 1");
$courses = $stmt->fetchAll();
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title"><?php echo $content ? 'Edit Content' : 'Add Content'; ?></h4>
                    <div>
                        <a href="details.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-list btn-icon-prepend"></i>List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="contentForm" class="forms-sample">
                        <?php if ($content): ?>
                        <input type="hidden" name="id" value="<?php echo $content['id']; ?>">
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="course_id">Course</label>
                                    <select class="form-control" name="course_id" id="course_id" required>
                                        <option value="">Select Course</option>
                                        <?php foreach ($courses as $course): ?>
                                        <option value="<?php echo $course['id']; ?>"
                                            <?php echo ($content && $content['course_id'] == $course['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($course['title']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="batch_id">Batch</label>
                                    <select class="form-control" name="batch_id" id="batch_id" required>
                                        <option value="">Select Batch</option>
                                        <?php foreach ($batches as $batch): ?>
                                        <option value="<?php echo $batch['id']; ?>"
                                            <?php echo ($content && $content['batch_id'] == $batch['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($batch['name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="subject_id">Subject</label>
                                    <select class="form-control" name="subject_id" required>
                                        <option value="">Select Subject</option>
                                        <?php foreach ($subjects as $subject): ?>
                                        <option value="<?php echo $subject['id']; ?>"
                                            <?php echo ($content && $content['subject_id'] == $subject['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($subject['name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="title">Lecture Title</label>
                                    <input type="text" class="form-control" name="title"
                                        value="<?php echo $content ? htmlspecialchars($content['title']) : ''; ?>"
                                        placeholder="Enter lecture title" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="youtube_url">YouTube URL</label>
                                    <input type="url" class="form-control" name="youtube_url"
                                        value="<?php echo $content ? htmlspecialchars($content['youtube_url']) : ''; ?>"
                                        placeholder="Enter YouTube video URL" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" name="description" rows="4"
                                        placeholder="Enter lecture description"><?php echo $content ? htmlspecialchars($content['description']) : ''; ?></textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lecture_date">Lecture Date</label>
                                    <input type="date" class="form-control" name="lecture_date"
                                        value="<?php echo $content ? $content['lecture_date'] : date('Y-m-d'); ?>"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" name="status">
                                        <option value="1"
                                            <?php echo ($content && $content['status'] == 1) ? 'selected' : ''; ?>>
                                            Active</option>
                                        <option value="0"
                                            <?php echo ($content && $content['status'] == 0) ? 'selected' : ''; ?>>
                                            Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                            class="btn btn-primary mr-2"><?php echo $content ? 'Update' : 'Submit'; ?></button>
                        <button type="button" class="btn btn-danger"
                            onclick="window.location.href='details.php'">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../include/footer.php'); ?>

<script>
$(document).ready(function() {
    $('#contentForm').on('submit', function(e) {
        e.preventDefault();

        Spinner.show();
        $(this).find('button[type="submit"]').prop('disabled', true);

        var formData = new FormData(this);
        var isEdit = formData.has('id');

        $.ajax({
            url: '../../api/batches-content/' + (isEdit ? 'update_content.php' :
                'add_content.php'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Spinner.hide();
                $('#contentForm').find('button[type="submit"]').prop('disabled', false);

                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }

                if (response.success) {
                    toastr.success('Content ' + (isEdit ? 'updated' : 'added') +
                        ' successfully!');
                    setTimeout(function() {
                        window.location.href = 'details.php';
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Failed to ' + (isEdit ? 'update' :
                        'add') + ' content!');
                }
            },
            error: function() {
                Spinner.hide();
                $('#contentForm').find('button[type="submit"]').prop('disabled', false);
                toastr.error('Something went wrong!');
            }
        });
    });

    $('#course_id').on('change', function() {
        var courseId = $(this).val();
        var batchSelect = $('#batch_id');

        // Clear batch dropdown
        batchSelect.html('<option value="">Select Batch</option>');

        if (courseId) {
            // Fetch batches for selected course
            $.ajax({
                url: '../../api/batches/get_batches_by_course.php',
                type: 'GET',
                data: {
                    course_id: courseId
                },
                success: function(response) {
                    if (response.success && response.batches) {
                        response.batches.forEach(function(batch) {
                            batchSelect.append(
                                $('<option></option>')
                                .val(batch.id)
                                .text(batch.name)
                            );
                        });
                    }
                },
                error: function() {
                    toastr.error('Failed to fetch batches!');
                }
            });
        }
    });
});
</script>