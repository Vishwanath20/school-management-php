<?php 
require_once('../../config/database.php');
include('../include/header.php');


// Fetch data if editing
$resource = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM free_resources WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $resource = $stmt->fetch();
}

// Fetch all active main resources for dropdown
$stmt = $pdo->query("SELECT id, title FROM free_resource_master WHERE status = 1 ORDER BY display_order ASC");
$mainResources = $stmt->fetchAll();
?>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<style>
p {
    color: black;
}

li {
    color: black;
}
</style>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title"><?php echo $resource ? 'Edit Resource' : 'Add Resource'; ?></h4>
                    <div>
                        <a href="details.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-list btn-icon-prepend"></i>List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="resourceForm" class="forms-sample" enctype="multipart/form-data">
                        <?php if ($resource): ?>
                            <input type="hidden" name="id" value="<?php echo $resource['id']; ?>">
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="master_id">Category</label>
                                    <select class="form-control" id="master_id" name="master_id" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($mainResources as $main): ?>
                                            <option value="<?php echo $main['id']; ?>" 
                                                <?php echo ($resource && $resource['master_id'] == $main['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($main['title']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sub_id">Sub Category</label>
                                    <select class="form-control" id="sub_id" name="sub_id" required>
                                        <option value="">Select Sub Category</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?php echo $resource ? htmlspecialchars($resource['title']) : ''; ?>" 
                                           required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="file">PDF File</label>
                                    <input type="file" class="form-control" id="file" name="file" 
                                           accept=".pdf" <?php echo $resource ? '' : ''; ?>>
                                    <?php if ($resource && $resource['file_path']): ?>
                                        <small class="form-text text-muted">Current file: <?php echo basename($resource['file_path']); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Photo</label>
                                    <?php if ($resource && $resource['photo']): ?>
                                        <div class="mb-2">
                                            <img src="../../uploads/freeresourcesimg/<?php echo htmlspecialchars($resource['photo']); ?>" 
                                                 alt="Current photo" style="max-width: 100px;">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="photo" id="photo" class="file-upload-default" 
                                           accept="image/*" <?php echo !$resource ? '' : ''; ?>>
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" disabled 
                                               placeholder="Upload Photo">
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-primary" type="button">Browse</button>
                                        </span>
                                    </div>
                                    <small class="form-text text-muted">Recommended: Square image (Width:340xHeight440px)</small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Short Description</label>
                                    <input type="text" class="form-control" id="description" name="description" 
                                           value="<?php echo $resource ? htmlspecialchars($resource['description']) : ''; ?>" 
                                           required>
                                </div>
                            </div>
                             <div class="col-md-12">
                                <div class="form-group">
                                    <label for="long_description">Text Content</label>
                                    <textarea type="text" class="form-control" id="long_description" name="long_description" 
                                            
                                           ><?php echo $resource ? htmlspecialchars($resource['long_description']) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mr-2"><?php echo $resource ? 'Update' : 'Submit'; ?></button>
                        <button type="button" class="btn btn-danger" onclick="window.location.href='details.php'">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('../include/footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>
$(document).ready(function() {

    $('#long_description').summernote({
        height: 300,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });

    $('.file-upload-browse').on('click', function() {
        var file = $(this).parents().find('.file-upload-default');
        file.trigger('click');
    });

    $('.file-upload-default').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).parents('.form-group').find('.file-upload-info').val(fileName);
    });

    // Function to load sub resources
    function loadSubResources(masterId, selectedSubId = '') {
        $('#sub_id').html('<option value="">Select Sub Resource</option>');
        
        if (masterId) {
            $.ajax({
                url: '../../api/freeresource/get-sub-resources.php',
                type: 'POST',
                data: { master_id: masterId },
                success: function(response) {
                    if (response.status === 'success') {
                        response.data.forEach(function(sub) {
                            var selected = (selectedSubId == sub.id) ? 'selected' : '';
                            $('#sub_id').append(`<option value="${sub.id}" ${selected}>${sub.title}</option>`);
                        });
                    }
                }
            });
        }
    }

    // Load sub resources on page load if editing
    <?php if ($resource): ?>
    loadSubResources(<?php echo $resource['master_id']; ?>, <?php echo $resource['sub_id']; ?>);
    <?php endif; ?>

    // Load sub resources when main resource changes
    $('#master_id').on('change', function() {
        loadSubResources($(this).val());
    });

    $('#resourceForm').on('submit', function(e) {
        e.preventDefault();
        
        Spinner.show();
        $(this).find('button[type="submit"]').prop('disabled', true);

        var formData = new FormData(this);

        $.ajax({
            url: '../../api/freeresource/' + ($(this).find('input[name="id"]').length ? 'update.php' : 'create.php'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Spinner.hide();
                $('#resourceForm').find('button[type="submit"]').prop('disabled', false);

                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        toastr.error('Invalid response from server');
                        return;
                    }
                }

                if (response.status === 'success') {
                    toastr.success(response.message || 'Resource ' + ($('#resourceForm input[name="id"]').length ? 'updated' : 'added') + ' successfully!');
                    setTimeout(function() {
                        window.location.href = 'details.php';
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Failed to ' + ($('#resourceForm input[name="id"]').length ? 'update' : 'add') + ' resource!');
                }
            },
            error: function(xhr) {
                Spinner.hide();
                $('#resourceForm').find('button[type="submit"]').prop('disabled', false);
                toastr.error('Something went wrong!');
            }
        });
    });
});
</script>

