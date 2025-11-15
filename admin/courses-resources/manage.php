<?php 
require_once('../../config/database.php');
include('../include/header.php');

// Fetch all active courses
$stmt = $pdo->query("SELECT id, title FROM courses WHERE status = 1");
$courses = $stmt->fetchAll();

// Fetch resource data if editing
$resource = null;
$resourceItems = [];
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM course_resources WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $resource = $stmt->fetch();

    if ($resource) {
        $stmt = $pdo->prepare("SELECT * FROM resource_items WHERE resource_id = ? ORDER BY sort_order");
        $stmt->execute([$resource['id']]);
        $resourceItems = $stmt->fetchAll();
    }
}
?>

<!-- Include necessary CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.css">
<style>
.resource-item {
    border: 1px solid #ddd;
    padding: 15px;
    margin-bottom: 10px;
    border-radius: 5px;
    background: #f9f9f9;
}
.resource-item .handle {
    cursor: move;
    padding: 5px;
}
.resource-preview {
    max-width: 200px;
    margin-top: 10px;
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
                                    <label for="course_id">Select Course</label>
                                    <select class="form-control" name="course_id" id="course_id" required>
                                        <option value="">Select Course</option>
                                        <?php foreach ($courses as $course): ?>
                                            <option value="<?php echo $course['id']; ?>" 
                                                <?php echo ($resource && $resource['course_id'] == $course['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($course['title']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Resource Title</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?php echo $resource ? htmlspecialchars($resource['title']) : ''; ?>" 
                                           placeholder="Enter resource title" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" 
                                              rows="4"><?php echo $resource ? htmlspecialchars($resource['description']) : ''; ?></textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Resource Items</label>
                                    <div id="resourceItems">
                                        <?php foreach ($resourceItems as $item): ?>
                                            <div class="resource-item" data-id="<?php echo $item['id']; ?>">
                                                <div class="row">
                                                    <div class="col-md-1">
                                                        <i class="mdi mdi-drag handle"></i>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select class="form-control item-type" name="items[<?php echo $item['id']; ?>][type]">
                                                            <option value="pdf" <?php echo $item['type'] == 'pdf' ? 'selected' : ''; ?>>PDF</option>
                                                            <option value="video" <?php echo $item['type'] == 'video' ? 'selected' : ''; ?>>Video</option>
                                                            <option value="image" <?php echo $item['type'] == 'image' ? 'selected' : ''; ?>>Image</option>
                                                            <option value="googleDrive" <?php echo $item['type'] == 'googleDrive' ? 'selected' : ''; ?>>Google Drive</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-7 content-container">
                                                        <?php if ($item['type'] == 'video' || $item['type'] == 'googleDrive'): ?>
                                                            <input type="text" class="form-control" name="items[<?php echo $item['id']; ?>][content]" 
                                                                   value="<?php echo htmlspecialchars($item['content']); ?>" placeholder="Enter YouTube URL">
                                                        <?php else: ?>
                                                            <input type="file" class="form-control" name="items[<?php echo $item['id']; ?>][content]">
                                                            <div class="resource-preview">
                                                                <?php if ($item['type'] == 'pdf'): ?>
                                                                    <a href="../../uploads/resources/<?php echo $item['content']; ?>" target="_blank">View Current PDF</a>
                                                                <?php else: ?>
                                                                    <img src="../../uploads/resources/<?php echo $item['content']; ?>" class="img-fluid">
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <button type="button" class="btn btn-danger btn-sm remove-item">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button type="button" class="btn btn-info" id="addItem">
                                        <i class="mdi mdi-plus"></i> Add Resource Item
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" name="status">
                                        <option value="1" <?php echo ($resource && $resource['status'] == 1) ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo ($resource && $resource['status'] == 0) ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
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

<!-- Include Sortable.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>

<script>
$(document).ready(function() {
    let itemCounter = <?php echo count($resourceItems); ?>;
    
    // Initialize Sortable
    new Sortable(document.getElementById('resourceItems'), {
        handle: '.handle',
        animation: 150
    });

    // Add new resource item
    $('#addItem').on('click', function() {
        itemCounter++;
        const newItem = `
            <div class="resource-item">
                <div class="row">
                    <div class="col-md-1">
                        <i class="mdi mdi-drag handle"></i>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control item-type" name="new_items[${itemCounter}][type]">
                            <option value="pdf">PDF</option>
                            <option value="video">Video</option>
                            <option value="image">Image</option>
                            <option value="googleDrive">Google Drive</option>
                        </select>
                    </div>
                    <div class="col-md-7 content-container">
                        <input type="file" class="form-control" name="new_items[${itemCounter}][content]" required>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm remove-item">
                            <i class="mdi mdi-delete"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('#resourceItems').append(newItem);
    });

    // Handle type change
    $(document).on('change', '.item-type', function() {
        const container = $(this).closest('.resource-item').find('.content-container');
        const itemId = $(this).closest('.resource-item').data('id');
        const isNew = !itemId;
        const name = isNew ? `new_items[${itemCounter}][content]` : `items[${itemId}][content]`;

        if ($(this).val() === 'video' || $(this).val() === 'googleDrive') {
            container.html(`
                <input type="text" class="form-control" name="${name}" 
                       placeholder="Enter link URL" required>
            `);
        } else {
            container.html(`
                <input type="file" class="form-control" name="${name}" required>
            `);
        }
    });

    // Remove item
    $(document).on('click', '.remove-item', function() {
        $(this).closest('.resource-item').remove();
    });

    // Form submission
    $('#resourceForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const isEdit = formData.has('id');
        
        // Add sort order
        $('#resourceItems .resource-item').each(function(index) {
            const itemId = $(this).data('id');
            if (itemId) {
                formData.append(`items[${itemId}][sort_order]`, index);
            }
        });

        $.ajax({
            url: '../../api/courses-resources/' + (isEdit ? 'update.php' : 'create.php'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    toastr.success('Resource ' + (isEdit ? 'updated' : 'added') + ' successfully!');
                   // setTimeout(() => window.location.href = 'details.php', 1500);
                } else {
                    toastr.error(response.message || 'Failed to ' + (isEdit ? 'update' : 'add') + ' resource!');
                }
            },
            error: function() {
                toastr.error('Something went wrong!');
            }
        });
    });
});
</script>