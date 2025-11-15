<?php 
require_once('../../config/database.php');
include('../include/header.php');

// Fetch subject data if editing
$subject = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM subjects WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $subject = $stmt->fetch();
}
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title"><?php echo $subject ? 'Edit Subject' : 'Add Subject'; ?></h4>
                    <div>
                        <a href="details.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-list btn-icon-prepend"></i>List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="subjectForm" class="forms-sample">
                        <?php if ($subject): ?>
                            <input type="hidden" name="id" value="<?php echo $subject['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Subject Name</label>
                                    <input type="text" class="form-control" name="name" 
                                           value="<?php echo $subject ? htmlspecialchars($subject['name']) : ''; ?>" 
                                           placeholder="Enter subject name" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" name="status">
                                        <option value="1" <?php echo ($subject && $subject['status'] == 1) ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo ($subject && $subject['status'] == 0) ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mr-2"><?php echo $subject ? 'Update' : 'Submit'; ?></button>
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
    $('#subjectForm').on('submit', function(e) {
        e.preventDefault();
        
        Spinner.show();
        $(this).find('button[type="submit"]').prop('disabled', true);

        var formData = new FormData(this);
        var isEdit = formData.has('id');

        $.ajax({
            url: '../../api/subject-master/' + (isEdit ? 'update_subject.php' : 'add_subject.php'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Spinner.hide();
                $('#subjectForm').find('button[type="submit"]').prop('disabled', false);
                
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                
                if (response.success) {
                    toastr.success('Subject ' + (isEdit ? 'updated' : 'added') + ' successfully!');
                    setTimeout(function() {
                        window.location.href = 'details.php';
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Failed to ' + (isEdit ? 'update' : 'add') + ' subject!');
                }
            },
            error: function() {
                Spinner.hide();
                $('#subjectForm').find('button[type="submit"]').prop('disabled', false);
                toastr.error('Something went wrong!');
            }
        });
    });
});
</script>