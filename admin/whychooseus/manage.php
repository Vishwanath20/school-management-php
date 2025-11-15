<?php 
require_once('../../config/database.php');
include('../include/header.php');


// Fetch data if editing
$feature = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM why_choose_us WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $feature = $stmt->fetch();
}
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title"><?php echo $feature ? 'Edit why choose us' : 'Add why choose us'; ?></h4>
                    <div>
                        <a href="details.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-list btn-icon-prepend"></i>List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="featureForm" class="forms-sample">
                        <?php if ($feature): ?>
                            <input type="hidden" name="id" value="<?php echo $feature['id']; ?>">
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Title<span style="color:red;">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?php echo $feature ? htmlspecialchars($feature['title']) : ''; ?>" 
                                           placeholder="Enter feature title" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Icon<span style="color:red;">*</span></label>
                                    <?php if ($feature && $feature['icon']): ?>
                                        <div class="mb-2">
                                            <img src="../../uploads/features/<?php echo htmlspecialchars($feature['icon']); ?>" 
                                                 alt="Current icon" style="max-width: 100px;">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="icon" id="icon" class="file-upload-default" 
                                           accept="image/*" <?php echo !$feature ? 'required' : ''; ?>>
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" disabled 
                                               placeholder="Upload Icon">
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-primary" type="button">Browse</button>
                                        </span>
                                    </div>
                                    <small class="form-text text-muted">Recommended: Square image (100x100px)</small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Description<span style="color:red;">*</span></label>
                                    <textarea class="form-control" id="description" name="description" 
                                              rows="4" required><?php echo $feature ? htmlspecialchars($feature['description']) : ''; ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="display_order">Display Order</label>
                                    <input type="number" class="form-control" id="display_order" name="display_order" 
                                           value="<?php echo $feature ? $feature['display_order'] : '0'; ?>" 
                                           min="0" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mr-2"><?php echo $feature ? 'Update' : 'Submit'; ?></button>
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
    $('#featureForm').on('submit', function(e) {
        e.preventDefault();
        
        Spinner.show();
        $(this).find('button[type="submit"]').prop('disabled', true);

        var formData = new FormData(this);
        var isEdit = formData.has('id');

        $.ajax({
            url: '../../api/whychooseus/' + (isEdit ? 'update.php' : 'create.php'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Spinner.hide();
                $('#featureForm').find('button[type="submit"]').prop('disabled', false);

                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        toastr.error('Invalid response from server');
                        return;
                    }
                }

                if (response.status === 'success') {
                    toastr.success(response.message || 'Feature ' + (isEdit ? 'updated' : 'added') + ' successfully!');
                    setTimeout(function() {
                        window.location.href = 'details.php';
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Failed to ' + (isEdit ? 'update' : 'add') + ' feature!');
                }
            },
            error: function(xhr) {
                Spinner.hide();
                $('#featureForm').find('button[type="submit"]').prop('disabled', false);
                
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
