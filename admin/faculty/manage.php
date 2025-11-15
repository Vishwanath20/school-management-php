<?php 
require_once('../../config/database.php');
include('../include/header.php');


// Fetch data if editing
$faculty = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM faculty WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $faculty = $stmt->fetch();
}
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title"><?php echo $faculty ? 'Edit Faculty' : 'Add Faculty'; ?></h4>
                    <div>
                        <a href="details.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-list btn-icon-prepend"></i>List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="facultyForm" class="forms-sample">
                        <?php if ($faculty): ?>
                            <input type="hidden" name="id" value="<?php echo $faculty['id']; ?>">
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Title (Dr./Prof.)</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?php echo $faculty ? htmlspecialchars($faculty['title']) : ''; ?>" 
                                           placeholder="Enter title" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo $faculty ? htmlspecialchars($faculty['name']) : ''; ?>" 
                                           placeholder="Enter name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="specialization">Specialization</label>
                                    <input type="text" class="form-control" id="specialization" name="specialization" 
                                           value="<?php echo $faculty ? htmlspecialchars($faculty['specialization']) : ''; ?>" 
                                           placeholder="Enter specialization" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="experience">Experience</label>
                                    <input type="text" class="form-control" id="experience" name="experience" 
                                           value="<?php echo $faculty ? htmlspecialchars($faculty['experience']) : ''; ?>" 
                                           placeholder="e.g., 15+ years experience" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Photo</label>
                                    <?php if ($faculty && $faculty['photo']): ?>
                                        <div class="mb-2">
                                            <img src="../../uploads/faculty/<?php echo htmlspecialchars($faculty['photo']); ?>" 
                                                 alt="Current photo" style="max-width: 100px;">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="photo" id="photo" class="file-upload-default" 
                                           accept="image/*" <?php echo !$faculty ? 'required' : ''; ?>>
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" disabled 
                                               placeholder="Upload Photo">
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-primary" type="button">Browse</button>
                                        </span>
                                    </div>
                                    <small class="form-text text-muted">Recommended: Square image (340x340px)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="linkedin">LinkedIn Profile URL</label>
                                    <input type="url" class="form-control" id="linkedin" name="linkedin" 
                                           value="<?php echo $faculty ? htmlspecialchars($faculty['linkedin']) : ''; ?>" 
                                           placeholder="Enter LinkedIn URL">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="twitter">Twitter Profile URL</label>
                                    <input type="url" class="form-control" id="twitter" name="twitter" 
                                           value="<?php echo $faculty ? htmlspecialchars($faculty['twitter']) : ''; ?>" 
                                           placeholder="Enter Twitter URL">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="display_order">Display Order</label>
                                    <input type="number" class="form-control" id="display_order" name="display_order" 
                                           value="<?php echo $faculty ? $faculty['display_order'] : '0'; ?>" 
                                           min="0" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mr-2"><?php echo $faculty ? 'Update' : 'Submit'; ?></button>
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
    $('#facultyForm').on('submit', function(e) {
        e.preventDefault();
        
        Spinner.show();
        $(this).find('button[type="submit"]').prop('disabled', true);

        var formData = new FormData(this);
        var isEdit = formData.has('id');

        $.ajax({
            url: '../../api/faculty/' + (isEdit ? 'update.php' : 'create.php'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Spinner.hide();
                $('#facultyForm').find('button[type="submit"]').prop('disabled', false);

                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        toastr.error('Invalid response from server');
                        return;
                    }
                }

                if (response.status === 'success') {
                    toastr.success(response.message || 'Faculty ' + (isEdit ? 'updated' : 'added') + ' successfully!');
                    setTimeout(function() {
                        window.location.href = 'details.php';
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Failed to ' + (isEdit ? 'update' : 'add') + ' faculty!');
                }
            },
            error: function(xhr) {
                Spinner.hide();
                $('#facultyForm').find('button[type="submit"]').prop('disabled', false);
                
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

