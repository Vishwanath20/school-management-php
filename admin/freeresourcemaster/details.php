<?php 
require_once('../../config/database.php');
include('../include/header.php');


// Fetch all resources
$stmt = $pdo->query("SELECT * FROM free_resource_master ORDER BY display_order ASC");
$resources = $stmt->fetchAll();
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;">
                    <h4 class="card-title">Free Resources Category</h4>
                    <div>
                        <a href="manage.php" class="btn btn-primary btn-icon-text">
                            <i class="mdi mdi-plus btn-icon-prepend"></i>Add Free Resource Category</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Category</th>
                                    <th>Icon</th>
                                    <th>Background</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($resources as $resource): ?>
                                <tr>
                                    <td><?php echo $resource['display_order']; ?></td>
                                    <td><?php echo htmlspecialchars($resource['title']); ?></td>
                                    <td><i class="<?php echo htmlspecialchars($resource['icon_class']); ?>"></i></td>
                                    <td>
                                        <div style="width: 30px; height: 30px; border-radius: 4px; background-color: <?php echo htmlspecialchars($resource['background_color']); ?>"></div>
                                    </td>
                                   
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input status-toggle" 
                                                   data-id="<?php echo $resource['id']; ?>"
                                                   <?php echo $resource['status'] ? 'checked' : ''; ?>>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="manage.php?id=<?php echo $resource['id']; ?>" 
                                           class="btn btn-info btn-sm">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm delete-resource" 
                                                data-id="<?php echo $resource['id']; ?>">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('../include/footer.php'); ?>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('.table').DataTable({
        "order": [[0, "asc"]],
        "pageLength": 25
    });

    // Handle status toggle
    $('.status-toggle').on('change', function() {
        var resourceId = $(this).data('id');
        var status = $(this).prop('checked') ? 1 : 0;
        
        $.ajax({
            url: '../../api/freeresourcemaster/update-status.php',
            type: 'POST',
            data: { 
                id: resourceId,
                status: status
            },
            success: function(response) {
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                
                if (response.status === 'success') {
                    toastr.success('Status updated successfully!');
                } else {
                    toastr.error(response.message || 'Failed to update status!');
                }
            },
            error: function() {
                toastr.error('Something went wrong!');
            }
        });
    });

    // Handle delete
    $('.delete-resource').on('click', function() {
        var resourceId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this resource?')) {
            $.ajax({
                url: '../../api/freeresourcemaster/delete.php',
                type: 'POST',
                data: { id: resourceId },
                success: function(response) {
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    
                    if (response.status === 'success') {
                        toastr.success('Resource deleted successfully!');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        toastr.error(response.message || 'Failed to delete resource!');
                    }
                },
                error: function() {
                    toastr.error('Something went wrong!');
                }
            });
        }
    });
});
</script>

