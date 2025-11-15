<?php 
require_once('../../config/database.php');
include('../include/header.php');


// Fetch data if editing
$resource = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM free_resource_master_sub WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $resource = $stmt->fetch();
}

// Fetch all active main resources for dropdown
$stmt = $pdo->query("SELECT id, title FROM free_resource_master WHERE status = 1 ORDER BY display_order ASC");
$mainResources = $stmt->fetchAll();
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title"><?php echo $resource ? 'Edit Sub Free Resource Sub Category' : 'Add Free Resource Sub Category'; ?></h4>
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
                                    <label for="title">Sub Category Name</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?php echo $resource ? htmlspecialchars($resource['title']) : ''; ?>" 
                                           placeholder="Enter title" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Sub Title</label>
                                    <input type="text" class="form-control" id="description" name="description" value="<?php 
                                        echo $resource ? htmlspecialchars($resource['description']) : ''; 
                                    ?>"/>
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

        var formData = new FormData(this);
       
        $.ajax({
            url: '../../api/freeresourcemastersub/' + ($(this).find('input[name="id"]').length ? 'update.php' : 'create.php'),
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

