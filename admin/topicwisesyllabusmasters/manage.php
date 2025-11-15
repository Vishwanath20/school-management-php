<?php 
include('../include/header.php');
require_once('../../config/database.php');

// Fetch data if editing
$master = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM topic_wise_syllabus_master WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $master = $stmt->fetch();
}

// Get max display order
$stmt = $pdo->query("SELECT MAX(display_order) as max_order FROM topic_wise_syllabus_master");
$maxOrder = $stmt->fetch();
$nextOrder = (isset($maxOrder['max_order']) ? $maxOrder['max_order'] : 0) + 1;
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title"><?php echo $master ? 'Edit Main Category' : 'Add Main Category'; ?></h4>
                    <div>
                        <a href="details.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-list btn-icon-prepend"></i>List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="masterForm" class="forms-sample">
                        <?php if ($master): ?>
                            <input type="hidden" name="id" value="<?php echo $master['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?php echo $master ? htmlspecialchars($master['title']) : ''; ?>" 
                                           placeholder="Enter title" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="display_order">Display Order</label>
                                    <input type="number" class="form-control" id="display_order" name="display_order" 
                                           value="<?php echo $master ? $master['display_order'] : $nextOrder; ?>" 
                                           min="1" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="file">PDF File</label>
                                    <input type="file" class="form-control" id="file" name="file" 
                                           accept=".pdf" <?php echo $master ? '' : 'required'; ?>>
                                    <?php if ($master && $master['file_path']): ?>
                                        <small class="form-text text-muted">Current file: <?php echo basename($master['file_path']); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary mr-2">
                            <?php echo $master ? 'Update' : 'Submit'; ?>
                        </button>
                        <button type="button" class="btn btn-danger" onclick="window.location.href='details.php'">
                            Cancel
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('../include/footer.php'); ?>
<script>
$(document).ready(function() {
    $('#masterForm').on('submit', function(e) {
        e.preventDefault();
        
        Spinner.show();
        $(this).find('button[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        $.ajax({
            url: '../../api/topicwisesyllabus/' + ($(this).find('input[name="id"]').length ? 'update-master.php' : 'create-master.php'),
            type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
            success: function(response) {
                Spinner.hide();
                $('#masterForm').find('button[type="submit"]').prop('disabled', false);

                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        toastr.error('Invalid response from server');
                        return;
                    }
                }

                if (response.status === 'success') {
                    toastr.success(response.message || 'Category ' + ($('#masterForm input[name="id"]').length ? 'updated' : 'added') + ' successfully!');
                    setTimeout(function() {
                        window.location.href = 'details.php';
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Failed to ' + ($('#masterForm input[name="id"]').length ? 'update' : 'add') + ' category!');
                }
            },
            error: function() {
                Spinner.hide();
                $('#masterForm').find('button[type="submit"]').prop('disabled', false);
                toastr.error('Something went wrong!');
            }
        });
    });
});
</script>

