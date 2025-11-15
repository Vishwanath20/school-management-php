<?php 
require_once('../../config/database.php');
include('../include/header.php');


// Fetch categories for dropdown
$stmt = $pdo->query("SELECT * FROM gallery_categories WHERE status = 1");
$categories = $stmt->fetchAll();

// Fetch photo data if editing
$photo = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM gallery_photos WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $photo = $stmt->fetch();
}
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title"><?php echo $photo ? 'Edit Photo' : 'Add Photo'; ?></h4>
                    <div>
                        <a href="details.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-list btn-icon-prepend"></i>List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="photoForm" class="forms-sample">
                        <?php if ($photo): ?>
                            <input type="hidden" name="id" value="<?php echo $photo['id']; ?>">
                        <?php endif; ?>
                        <div class="row">                        
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Category</label>
                                    <select class="form-control" name="category_id" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>" 
                                                <?php echo ($photo && $photo['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category['title']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                          
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" name="status">
                                        <option value="1" <?php echo ($photo && $photo['status'] == 1) ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo ($photo && $photo['status'] == 0) ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Photos</label>
                                    <input type="file" class="form-control" name="images[]" 
                                           accept="image/*" multiple <?php echo $photo ? '' : 'required'; ?>>
                                    <small class="text-muted">You can select multiple photos at once</small>
                                    <?php if ($photo): ?>
                                        <img src="../../uploads/gallery/<?php echo $photo['image_path']; ?>" 
                                             class="mt-2" style="max-width: 100px;">
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mr-2"><?php echo $photo ? 'Update' : 'Submit'; ?></button>
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
    $('#photoForm').on('submit', function(e) {
        e.preventDefault();
        
        Spinner.show();
        $(this).find('button[type="submit"]').prop('disabled', true);

        var formData = new FormData(this);
        var isEdit = formData.has('id');

        $.ajax({
            url: '../../api/gallery/' + (isEdit ? 'update_photo.php' : 'add_photo.php'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Spinner.hide();
                $('#photoForm').find('button[type="submit"]').prop('disabled', false);

                if (response.success) {
                    toastr.success('Photo ' + (isEdit ? 'updated' : 'added') + ' successfully!');
                    setTimeout(function() {
                        window.location.href = 'details.php';
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Failed to ' + (isEdit ? 'update' : 'add') + ' photo!');
                }
            },
            error: function(xhr) {
                Spinner.hide();
                $('#photoForm').find('button[type="submit"]').prop('disabled', false);
                
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
