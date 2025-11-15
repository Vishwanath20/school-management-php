<?php 
require_once('../../config/database.php');
include('../include/header.php');


// Fetch slider data if editing
$slider = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM hero_sliders WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $slider = $stmt->fetch();
}
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title"><?php echo $slider ? 'Edit Banner' : 'Add Banner'; ?></h4>
                    <div>
                        <a href="details.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-list btn-icon-prepend"></i>List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="sliderForm" class="forms-sample">
                        <?php if ($slider): ?>
                            <input type="hidden" name="id" value="<?php echo $slider['id']; ?>">
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?php echo $slider ? htmlspecialchars($slider['title']) : ''; ?>" 
                                           placeholder="Enter title">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subtitle">Subtitle</label>
                                    <input type="text" class="form-control" id="subtitle" name="subtitle" 
                                           value="<?php echo $slider ? htmlspecialchars($slider['subtitle']) : ''; ?>" 
                                           placeholder="Enter subtitle">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Banner Image<span style="color:red;">*</span></label>
                                    <?php if ($slider && $slider['image']): ?>
                                        <div class="mb-2">
                                            <img src="../../uploads/sliders/<?php echo htmlspecialchars($slider['image']); ?>" 
                                                 alt="Current slider" style="max-width: 200px;">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="image" id="image" class="file-upload-default" accept="image/*" <?php echo !$slider ? 'required' : ''; ?>>
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-primary" type="button">Browse</button>
                                        </span>
                                    </div>
                                    <small class="form-text text-muted">Size Width:4000px; Height:1500px</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="button_text">Button Text</label>
                                    <input type="text" class="form-control" id="button_text" name="button_text" 
                                           value="<?php echo $slider ? htmlspecialchars($slider['button_text']) : ''; ?>" 
                                           placeholder="Enter button text">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="display_order">Display Order</label>
                                    <input type="number" class="form-control" id="display_order" name="display_order" 
                                           value="<?php echo $slider ? htmlspecialchars($slider['display_order']) : ''; ?>" 
                                           placeholder="Enter display order">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="button_link">Button Link</label>
                                    <input type="url" class="form-control" id="button_link" name="button_link" 
                                           value="<?php echo $slider ? htmlspecialchars($slider['button_link']) : ''; ?>" 
                                           placeholder="Enter button link">
                                </div>
                            </div>
                            <div class="col-md-12" style="display:none;">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4"><?php echo $slider ? htmlspecialchars($slider['description']) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mr-2"><?php echo $slider ? 'Update' : 'Submit'; ?></button>
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
    $('#sliderForm').on('submit', function(e) {
        e.preventDefault();
        
        Spinner.show();
        $(this).find('button[type="submit"]').prop('disabled', true);

        var formData = new FormData(this);
        var isEdit = formData.has('id');

        $.ajax({
            url: '../../api/heroslider/' + (isEdit ? 'update.php' : 'create.php'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Spinner.hide();
                $('#sliderForm').find('button[type="submit"]').prop('disabled', false);

                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        toastr.error('Invalid response from server');
                        return;
                    }
                }

                if (response.status === 'success') {
                    toastr.success(response.message || 'Slider ' + (isEdit ? 'updated' : 'added') + ' successfully!');
                    setTimeout(function() {
                        window.location.href = 'details.php';
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Failed to ' + (isEdit ? 'update' : 'add') + ' slider!');
                }
            },
            error: function(xhr) {
                Spinner.hide();
                $('#sliderForm').find('button[type="submit"]').prop('disabled', false);
                
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

