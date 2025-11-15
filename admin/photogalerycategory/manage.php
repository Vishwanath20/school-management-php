<?php 
require_once('../../config/database.php');
include('../include/header.php');


// Fetch category data if editing
$category = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM gallery_categories WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $category = $stmt->fetch();
}
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title"><?php echo $category ? 'Edit Category' : 'Add Category'; ?></h4>
                    <div>
                        <a href="category.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-list btn-icon-prepend"></i>List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="categoryForm" class="forms-sample">
                        <?php if ($category): ?>
                            <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?php echo $category ? htmlspecialchars($category['title']) : ''; ?>" 
                                           placeholder="Enter category title" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" name="status">
                                        <option value="1" <?php echo ($category && $category['status'] == 1) ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo ($category && $category['status'] == 0) ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" 
                                              rows="4"><?php echo $category ? htmlspecialchars($category['description']) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mr-2"><?php echo $category ? 'Update' : 'Submit'; ?></button>
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
    $('#categoryForm').on('submit', function(e) {
        e.preventDefault();
        
        Spinner.show();
        $(this).find('button[type="submit"]').prop('disabled', true);

        var formData = new FormData(this);
        var isEdit = formData.has('id');

        $.ajax({
            url: '../../api/gallery/' + (isEdit ? 'update_category.php' : 'add_category.php'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Spinner.hide();
                $('#categoryForm').find('button[type="submit"]').prop('disabled', false);

                if (response.success) {
                    toastr.success('Category ' + (isEdit ? 'updated' : 'added') + ' successfully!');
                    setTimeout(function() {
                        window.location.href = 'details.php';
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Failed to ' + (isEdit ? 'update' : 'add') + ' category!');
                }
            },
            error: function(xhr) {
                Spinner.hide();
                $('#categoryForm').find('button[type="submit"]').prop('disabled', false);
                
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