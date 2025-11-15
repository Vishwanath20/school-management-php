<?php 

require_once('../../config/database.php');
include('../include/header.php');
// Fetch data if editing
$review = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM toppers_reviews WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $review = $stmt->fetch();
}
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title"><?php echo $review ? 'Edit Review' : 'Add Review'; ?></h4>
                    <div>
                        <a href="details.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-list btn-icon-prepend"></i>List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="reviewForm" class="forms-sample">
                        <?php if ($review): ?>
                            <input type="hidden" name="id" value="<?php echo $review['id']; ?>">
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Topper's Name</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo $review ? htmlspecialchars($review['name']) : ''; ?>" 
                                           placeholder="Enter topper's name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exam_rank">Rank</label>
                                    <input type="number" class="form-control" id="exam_rank" name="exam_rank" 
                                           value="<?php echo $review ? $review['exam_rank'] : ''; ?>" 
                                           placeholder="Enter rank" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exam">Exam</label>
                                    <input type="text" class="form-control" id="exam" name="exam" 
                                           value="<?php echo $review ? htmlspecialchars($review['exam']) : 'UPSC CSE'; ?>" 
                                           placeholder="Enter exam name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="year">Year</label>
                                    <input type="number" class="form-control" id="year" name="year" 
                                           value="<?php echo $review ? $review['year'] : date('Y'); ?>" 
                                           placeholder="Enter year" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Profile Photo</label>
                                    <?php if ($review && $review['photo']): ?>
                                        <div class="mb-2">
                                            <img src="../../uploads/toppers/<?php echo htmlspecialchars($review['photo']); ?>" 
                                                 alt="Current photo" style="max-width: 100px;" class="rounded-circle">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="photo" id="photo" class="file-upload-default" 
                                           accept="image/*" <?php echo !$review ? 'required' : ''; ?>>
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" disabled 
                                               placeholder="Upload Profile Photo">
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-primary" type="button">Browse</button>
                                        </span>
                                    </div>
                                    <small class="form-text text-muted">Recommended: Square image (100x100px)</small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="review">Review</label>
                                    <textarea class="form-control" id="review" name="review" rows="4" 
                                              required><?php echo $review ? htmlspecialchars($review['review']) : ''; ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="display_order">Display Order</label>
                                    <input type="number" class="form-control" id="display_order" name="display_order" 
                                           value="<?php echo $review ? $review['display_order'] : '0'; ?>" 
                                           min="0" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mr-2"><?php echo $review ? 'Update' : 'Submit'; ?></button>
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
    // File upload handling
    $('.file-upload-browse').on('click', function() {
        var file = $(this).parents().find('.file-upload-default');
        file.trigger('click');
    });

    $('.file-upload-default').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).parents('.form-group').find('.file-upload-info').val(fileName);
    });

    // Form submission
    $('#reviewForm').on('submit', function(e) {
        e.preventDefault();
        
        Spinner.show();
        $(this).find('button[type="submit"]').prop('disabled', true);

        var formData = new FormData(this);
        var isEdit = formData.has('id');

        $.ajax({
            url: '../../api/toppersreviews/' + (isEdit ? 'update.php' : 'create.php'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Spinner.hide();
                $('#reviewForm').find('button[type="submit"]').prop('disabled', false);

                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        toastr.error('Invalid response from server');
                        return;
                    }
                }

                if (response.status === 'success') {
                    toastr.success(response.message || 'Review ' + (isEdit ? 'updated' : 'added') + ' successfully!');
                    setTimeout(function() {
                        window.location.href = 'details.php';
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Failed to ' + (isEdit ? 'update' : 'add') + ' review!');
                }
            },
            error: function(xhr) {
                Spinner.hide();
                $('#reviewForm').find('button[type="submit"]').prop('disabled', false);
                
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

