<?php 
require_once('../../config/database.php');
include('../include/header.php');

// Fetch batch data if editing
$batch = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM batches WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $batch = $stmt->fetch();
}

// Fetch courses for dropdown
$stmt = $pdo->query("SELECT id, title FROM courses WHERE status = 1");
$courses = $stmt->fetchAll();

// Fetch faculty for dropdown
$stmt = $pdo->query("SELECT id, name FROM faculty WHERE status = 1");
$faculty = $stmt->fetchAll();
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title"><?php echo $batch ? 'Edit Batch' : 'Add Batch'; ?></h4>
                    <div>
                        <a href="details.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-list btn-icon-prepend"></i>List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="batchForm" class="forms-sample">
                        <?php if ($batch): ?>
                            <input type="hidden" name="id" value="<?php echo $batch['id']; ?>">
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Batch Name</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo $batch ? htmlspecialchars($batch['name']) : ''; ?>" 
                                           placeholder="Enter batch name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="course_id">Course</label>
                                    <select class="form-control" name="course_id" required>
                                        <option value="">Select Course</option>
                                        <?php foreach ($courses as $course): ?>
                                            <option value="<?php echo $course['id']; ?>" 
                                                <?php echo ($batch && $batch['course_id'] == $course['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($course['title']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date"
                                           value="<?php echo $batch ? $batch['start_date'] : ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                           value="<?php echo $batch ? $batch['end_date'] : ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="timing">Timing</label>
                                    <input type="time" class="form-control" id="timing" name="timing"
                                           value="<?php echo $batch ? $batch['timing'] : ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="capacity">Capacity</label>
                                    <input type="number" class="form-control" id="capacity" name="capacity"
                                           value="<?php echo $batch ? $batch['capacity'] : ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="faculty_id">Faculty</label>
                                    <select class="form-control" name="faculty_id" required>
                                        <option value="">Select Faculty</option>
                                        <?php foreach ($faculty as $teacher): ?>
                                            <option value="<?php echo $teacher['id']; ?>"
                                                <?php echo ($batch && $batch['faculty_id'] == $teacher['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($teacher['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" name="status">
                                        <option value="1" <?php echo ($batch && $batch['status'] == 1) ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo ($batch && $batch['status'] == 0) ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mr-2"><?php echo $batch ? 'Update' : 'Submit'; ?></button>
                        <button type="button" class="btn btn-danger" onclick="window.location.href='details.php'">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('../include/footer.php'); ?>
<script>
$(document).ready(function() {
    $('#batchForm').on('submit', function(e) {
        e.preventDefault();
        
        Spinner.show();
        $(this).find('button[type="submit"]').prop('disabled', true);

        var formData = new FormData(this);
        var isEdit = formData.has('id');

        $.ajax({
            url: '../../api/batches/' + (isEdit ? 'update_batch.php' : 'add_batch.php'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Spinner.hide();
                $('#batchForm').find('button[type="submit"]').prop('disabled', false);
                response = JSON.parse(response);
                if (response.success) {
                    toastr.success('Batch ' + (isEdit ? 'updated' : 'added') + ' successfully!');
                    // setTimeout(function() {
                    //     window.location.href = 'details.php';
                    // }, 1500);
                } else {
                    toastr.error(response.message || 'Failed to ' + (isEdit ? 'update' : 'add') + ' batch!');
                }
            },
            error: function(xhr) {
                Spinner.hide();
                $('#batchForm').find('button[type="submit"]').prop('disabled', false);
                
                try {
                    var response = JSON.parse(xhr.responseText);
                    toastr.error(response.message || 'Something went wrong!');
                } catch (e) {
                    toastr.error('Something went wrong!');
                }
            }
        });
    });
});
</script>