<?php 
require_once('../../config/database.php');
include('../include/header.php');


// Fetch data if editing
$exam = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM upcoming_exams WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $exam = $stmt->fetch();
}
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title"><?php echo $exam ? 'Edit Exam' : 'Add Exam'; ?></h4>
                    <div>
                        <a href="details.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-list btn-icon-prepend"></i>List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="examForm" class="forms-sample">
                        <?php if ($exam): ?>
                            <input type="hidden" name="id" value="<?php echo $exam['id']; ?>">
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Exam Title</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?php echo $exam ? htmlspecialchars($exam['title']) : ''; ?>" 
                                           placeholder="e.g., UPSC CSE Prelims 2025" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exam_date">Exam Date</label>
                                    <input type="datetime-local" class="form-control" id="exam_date" name="exam_date" 
                                           value="<?php echo $exam ? date('Y-m-d\TH:i', strtotime($exam['exam_date'])) : ''; ?>" 
                                           required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="display_order">Display Order</label>
                                    <input type="number" class="form-control" id="display_order" name="display_order" 
                                           value="<?php echo $exam ? $exam['display_order'] : '0'; ?>" 
                                           min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6" style="display:none;">
                                <div class="form-group">
                                    <label for="background_color">Background Color</label>
                                    <input type="color" class="form-control" id="background_color" name="background_color" 
                                           value="<?php echo $exam ? $exam['background_color'] : '#6f42c1'; ?>" 
                                           required>
                                    <small class="form-text text-muted">Choose background color for countdown timer</small>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mr-2"><?php echo $exam ? 'Update' : 'Submit'; ?></button>
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
    // Form submission
    $('#examForm').on('submit', function(e) {
        e.preventDefault();
        
        Spinner.show();
        $(this).find('button[type="submit"]').prop('disabled', true);

        var formData = new FormData(this);
        var isEdit = formData.has('id');

        $.ajax({
            url: '../../api/upcomingexams/' + (isEdit ? 'update.php' : 'create.php'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Spinner.hide();
                $('#examForm').find('button[type="submit"]').prop('disabled', false);

                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        toastr.error('Invalid response from server');
                        return;
                    }
                }

                if (response.status === 'success') {
                    toastr.success(response.message || 'Exam ' + (isEdit ? 'updated' : 'added') + ' successfully!');
                    setTimeout(function() {
                        window.location.href = 'details.php';
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Failed to ' + (isEdit ? 'update' : 'add') + ' exam!');
                }
            },
            error: function(xhr) {
                Spinner.hide();
                $('#examForm').find('button[type="submit"]').prop('disabled', false);
                
                try {
                    var errorResponse = JSON.parse(xhr.responseText);
                    toastr.error(errorResponse.message || 'Something went wrong!');
                } catch (e) {
                    toastr.error('Something went wrong!');
                }
            }
        });
    });
});
</script>

