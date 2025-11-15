<?php 
require_once('../../config/database.php');
include('../include/header.php');


// Fetch data if editing
$resource = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM free_resource_master WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $resource = $stmt->fetch();
}
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title"><?php echo $resource ? 'Edit Free Resource Category' : 'Add Free Resource Category'; ?></h4>
                    <div>
                        <a href="details.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-list btn-icon-prepend"></i>List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="resourceForm" class="forms-sample">
                        <?php if ($resource): ?>
                            <input type="hidden" name="id" value="<?php echo $resource['id']; ?>">
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Category</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?php echo $resource ? htmlspecialchars($resource['title']) : ''; ?>" 
                                           placeholder="e.g., Books & Magazines" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="icon_class">Icon Class</label>
                                    <input type="text" class="form-control" id="icon_class" name="icon_class" 
                                           value="<?php echo $resource ? htmlspecialchars($resource['icon_class']) : ''; ?>" 
                                           placeholder="e.g., mdi mdi-book" required>
                                    <small class="form-text text-muted">Use Material Design Icons classes <a target="_blank" href="../icons/mdi.php">Get Icons</a></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="background_color">Background Color</label>
                                    <input type="color" class="form-control" id="background_color" name="background_color" 
                                           value="<?php echo $resource ? $resource['background_color'] : '#e3f2fd'; ?>" 
                                           required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="display_order">Display Order</label>
                                    <input type="number" class="form-control" id="display_order" name="display_order" 
                                           value="<?php echo $resource ? $resource['display_order'] : '0'; ?>" 
                                           min="0" required>
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
<script>
$(document).ready(function() {
    $('#resourceForm').on('submit', function(e) {
        e.preventDefault();
        
        Spinner.show();
        $(this).find('button[type="submit"]').prop('disabled', true);

        $.ajax({
            url: '../../api/freeresourcemaster/' + ($(this).find('input[name="id"]').length ? 'update.php' : 'create.php'),
            type: 'POST',
            data: $(this).serialize(),
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
